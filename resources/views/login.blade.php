<div>
    <a href="{{route('social.login', 'facebook')}}">
        Login dengan facebook
    </a> <br>
    <a href="{{route('social.login', 'google')}}">
        Login dengan google
    </a>

    <form action="{{route('payment.upload')}}" method="post">
        <input type="file" name="file" id="file">
        <button type="submit">Kirim</button>
    </form>
</div>
