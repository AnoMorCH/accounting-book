<?php

echo "
	<header>
		<a href='#'>Санаторий</a>
	</header>
	<input type='checkbox' id='nav' hidden checked />
	<div overflow>
		<ul>
			<li><a href='{$urls["login_page"]}'>Авторизация</a></li>
			<li><a href='{$urls["signup_page"]}'>Регистрация</a></li>
		</ul>
	</div>
";
