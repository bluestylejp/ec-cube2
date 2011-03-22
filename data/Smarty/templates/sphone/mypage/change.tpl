<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
*}-->
<!--▼CONTENTS-->
<div id="mypagecolumn">
  <h2 class="title"><!--{$tpl_title|h}--></h2>
<!--{include file=$tpl_navi}-->
<div id="mycontentsarea">
  <h2><!--{$tpl_subtitle|h}--></h2>
  <p>下記項目にご入力ください。「<span class="attention">※</span>」印は入力必須項目です。<br />
    入力後、一番下の「確認ページへ」ボタンをクリックしてください。</p>

  <form name="form1" id="form1" method="post" action="?">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="confirm" />
  <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />
    <table summary="会員登録内容変更 " class="entryform">
      <!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`frontparts/form_personal_input.tpl" flgFields=3 emailMobile=true prefix=""}-->
    </table>
    <!--{if 'sfGMOMypageDisplay'|function_exists}-->
      <!--{'sfGMOMypageDisplay'|call_user_func}-->
    <!--{/if}-->
    <div class="tblareabtn">
      <input class="spbtn spbtn-shopping" type="submit" class="box150" value="確認ページへ" name="refusal" id="refusal" />
    </div>
  </form>
</div>

</div>
<!--▲CONTENTS-->
