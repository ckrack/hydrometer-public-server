<a class="navbar-brand mb-0" href="/"><?=getenv('SITE_TITLE')?></a>

<div class="collapse navbar-collapse justify-content-start" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto ml-0">
        <li class="nav-item">
            <a class="nav-link" href="/about"><?=_('About')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/faq"><?=_('FAQ')?></a>
        </li>
        <li class="nav-item ml-2">
            <a class="nav-link" href="/guide"><?=_('Guide')?></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto mr-0">
        <li class="nav-item mr-2">
            <a class="nav-link btn btn-primary" href="/auth/login"><?=_('Login')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn btn-success" href="/auth/register"><?=_('Register')?></a>
        </li>
    </ul>
</div>
