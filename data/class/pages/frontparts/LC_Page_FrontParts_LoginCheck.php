<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once(CLASS_EX_REALDIR . "page_extends/LC_Page_Ex.php");

/**
 * ログインチェック のページクラス.
 *
 * TODO mypage/LC_Page_Mypage_LoginCheck と統合
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_FrontParts_LoginCheck.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_FrontParts_LoginCheck extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        // 会員管理クラス
        $objCustomer = new SC_Customer_Ex();
        // クッキー管理クラス
        $objCookie = new SC_Cookie_Ex(COOKIE_EXPIRE);
        // パラメータ管理クラス
        $objFormParam = new SC_FormParam_Ex();

        // パラメータ情報の初期化
        $this->lfInitParam($objFormParam);

        // リクエスト値をフォームにセット
        $objFormParam->setParam($_POST);

        // モードによって分岐
        switch ($this->getMode()) {
        case 'login':
            // --- ログイン

            // 入力値のエラーチェック
            $objFormParam->trimParam();
            $objFormParam->toLower('login_email');
            $arrErr = $objFormParam->checkError();

            // エラーの場合はエラー画面に遷移
            if (count($arrErr) > 0) {
                SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
            }

            // 入力チェック後の値を取得
            $arrForm = $objFormParam->getHashArray();

            // クッキー保存判定
            if ($arrForm['login_memory'] == '1' && $arrForm['login_email'] != '') {
                $objCookie->setCookie('login_email', $arrForm['login_email']);
            } else {
                $objCookie->setCookie('login_email', '');
            }

            // 遷移先の制御
            if (count($arrErr) == 0) {
                // ログイン判定
                $loginFailFlag = false;
                if(SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
                    // モバイルサイト
                    if(!$objCustomer->getCustomerDataFromMobilePhoneIdPass($arrForm['login_pass']) &&
                       !$objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'], true)) {
                        $loginFailFlag = true;
                    }
                } else {
                    // モバイルサイト以外
                    if(!$objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'])) {
                        $loginFailFlag = true;
                    }
                }

                // ログイン処理
                if ($loginFailFlag == false) {
                    if(SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
                        // ログインが成功した場合は携帯端末IDを保存する。
                        $objCustomer->updateMobilePhoneId();

                        /*
                         * email がモバイルドメインでは無く,
                         * 携帯メールアドレスが登録されていない場合
                         */
                        $objMobile = new SC_Helper_Mobile_Ex();
                        if (!$objMobile->gfIsMobileMailAddress($objCustomer->getValue('email'))) {
                            if (!$objCustomer->hasValue('email_mobile')) {
                                SC_Response_Ex::sendRedirectFromUrlPath('entry/email_mobile.php');
                                exit;
                            }
                        }
                    }

                    // --- ログインに成功した場合
                    SC_Response_Ex::sendRedirect($_POST['url']);
                    exit;
                } else {
                    // --- ログインに失敗した場合
                    $arrForm['login_email'] = strtolower($arrForm['login_email']);
                    $objQuery = SC_Query::getSingletonInstance();
                    $where = '(email = ? OR email_mobile = ?) AND status = 1 AND del_flg = 0';
                    $ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email'], $arrForm['login_email']));
                    // ログインエラー表示
                    if($ret > 0) {
                        SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
                    } else {
                        SC_Utils_Ex::sfDispSiteError(SITE_LOGIN_ERROR);
                    }
                }
            } else {
                // 入力エラーの場合、元のアドレスに戻す。
                // FIXME $_POST['url'] には、URL ではなく、url-path が渡るもよう。HTTPS 利用に関わる問題も考えられるので、URL が渡るように改善した方が良いように感じる。
                SC_Response_Ex::sendRedirect($_POST['url']);
                exit;
            }

            break;
        case 'logout':
            // --- ログアウト

            // ログイン情報の解放
            $objCustomer->EndSession();
            // 画面遷移の制御
            $mypage_url_search = strpos('.'.$_POST['url'], 'mypage');
            if ($mypage_url_search == 2) {
                // マイページログイン中はログイン画面へ移行
                SC_Response_Ex::sendRedirectFromUrlPath('mypage/login.php');
            } else {
                // 上記以外の場合、トップへ遷移
                SC_Response_Ex::sendRedirect(HTTP_URL);
            }
            exit;

            break;
        default:
            break;
        }

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * パラメータ情報の初期化.
     *
     * @param SC_FormParam $objFormParam パラメータ管理クラス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('記憶する', 'login_memory', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('メールアドレス', 'login_email', MTEXT_LEN, 'a', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('パスワード', 'login_pass', PASSWORD_MAX_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    }
}
?>
