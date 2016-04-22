<?php
function red_vcard_view($account){
       if (isset($account->uid)) {
          return theme('vcard', $account, FALSE);
        }
        return '';
}