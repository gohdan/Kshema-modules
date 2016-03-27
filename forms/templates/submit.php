<h1>Подтверждение анкеты</h1>

<p>#result#</p>

<p>#content#</p>

<p>
#form#
</p>

<form action="/index.php?module=forms&action=send" method="post">
<input type="hidden" name="send" value="yes">
<input type="hidden" name="formname" value="#formname#">
<input type="hidden" name="fio" value="#fio#">
<input type="hidden" name="email" value="#email#">
<input type="hidden" name="fields" value="#fields#">
<input type="hidden" name="values" value="#values#">
<input type="submit" name="do_send" value="Отправить">
</form>