<span>
  Dear Administrator,
</span>
<p>
  Berikut informasi terkait stock pada aplikasi {{config('settings.app_name')}}. List stock yang mencapai batas minimum sebagai berikut :
</p>
<div>
  @foreach($list as $product)
    <ul>
      <li>FG_CODE : {{$product['fg_code']}}</li>
      <li>Name : {{$product['name']}}</li>
      <li>Stock : {{$product['stock']}}</li>
    </ul>
  @endforeach
</div>
<span>
  Hormat kami,<br/>
</span>
<span>
  Smartfren
</span>
