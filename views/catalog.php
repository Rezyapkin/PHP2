<h2>Каталог</h2>
<p>
Теперь моя модель умеет работать вот такими методами:
</p>
<ul>
<ll>Products::where('price','>',50000)->orWhere('id','3')->orderBy('price')->get();</li>
<li>Products::where('price','>',50000)->where('price','<',100000)->orderBy('name')->fisrt();</li>
<li>Products::where('price','>',50000)->where('price','<',60000)->count();</li>
<li>Products::where('price','>',50000)->max('price');</li>
</ul>
<p>Также реализовал пагинацию и ушел от статики через __callStatic</p>
<div id="catalog" data-page_size="<?=$page_size?>">
<? foreach ($catalog as $item): ?>
    <h3><a href="/?c=product&a=card&id=<?=$item['id']?>"><?=$item['name']?></a></h3>
    <p><?=$item['description']?></p>
    <p>Цена: <?=$item['price']?></p>
    <hr>
<? endforeach;?>
</div>
<script src="/js/catalog.js"></script>
