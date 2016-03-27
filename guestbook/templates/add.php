<h1>Добавление сообщения</h1>

{{if:show_admin_link:
<p>
<a href="/guestbook/admin/">Меню администрирования сообщений</a><br>
</p>
}}

<p><a href="/guestbook/view/">Обратно в гостевую книгу</a></p>

{{if:no_text:<p style="font-weight: bold">Пожалуйста, введите текст</p>}}
{{if:success:<p style="font-weight: bold">Сообщение успешно добавлено. После утверждения администратором оно будет показываться на сайте</p>}}
{{if:error:<p style="font-weight: bold">Не удалось добавить сообщение, ошибка базы данных</p>}}
{{if:captcha_error:<p style="font-weight: bold">Ошибка проверка капчи: #captcha_error#</p>}}
{{if:captcha_wrong:<p style="font-weight: bold">Пожалуйста, поставьте галочку "Я не робот"</p>}}

{{if:recaptcha:<script src='https://www.google.com/recaptcha/api.js'></script>}}

<form action="/guestbook/add/" method="post">
<table summary="message add table">
<tr><td>Ваше имя:</td><td><input type="text" name="name" value="#name#"></td></tr>
<tr><td>Контактные данные:</td><td><input type="text" name="contact" value="#contact#"></td></tr>
<tr><td>Текст (обязательно):</td><td><textarea name="text" style="width: 400px; height: 300px">#text#</textarea></td></tr>
{{if:recaptcha:<tr><td></td><td><div class="g-recaptcha" data-sitekey="#recaptcha_sitekey#"></div></td></tr>}}
<tr><td></td><td><input type="submit" name="do_add" value="Добавить"></td></tr>
</table>
</form>
