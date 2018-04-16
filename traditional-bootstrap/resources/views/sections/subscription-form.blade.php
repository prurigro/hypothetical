<div class="subscription-form-section-component">
    <form id="subscription-form" action="#" method="POST" accept-charset="UTF-8">
        <div class="notification"></div>
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="text" name="email" placeholder="Email" />
        <input type="text" name="name" placeholder="Name" />
        <input type="submit" name="submit" value="Subscribe" />
    </form>
</div>
