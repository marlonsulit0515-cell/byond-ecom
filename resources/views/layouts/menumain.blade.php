<style>
  a{
    text-decoration: none;
    color: black;
  }
  a:hover {
    color: rgb(5, 15, 146); /* change color when hovering */
}
</style>
<div class="menuMain">
<a href="{{ route ('home') }}"><img src="{{asset('img/logo/Byond-Logo.png') }}" alt=""></a>
  <ul>
    <li><a href="{{ route ('home') }}">Home</a></li>
    <li><a href="{{ url ('/shop-page')}}">Shop</a></li>
    <li><a href="{{ route ('view.contact') }}">Contact Us</a></li>
    <li><a href="{{ route ('aboutus') }}">About Us</a></li>
  </ul>
</div>
