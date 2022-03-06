<?php
require '../functions.php';

$category_id = $_GET["category_id"];


$query = "SELECT * FROM product LEFT JOIN category USING (category_id) WHERE 
                category_id = $category_id
            LIMIT 9";

if (!$category_id) {
    $query = "SELECT * FROM product LEFT JOIN category USING (category_id) LIMIT 12";
};
$products = query($query);

?>
<div class="product__container">
    <?php foreach ($products as $product) : ?>
        <?php if ($product["product_status"] == 0) {
            continue;
        } ?>
        <div class="product__card card">
            <div class="product__card-image card__image-container"><img src="dashboard/produk/img/<?= $product["product_image"]; ?>" class="card__image" alt=""></div>
            <div class="card__text">
                <a href="?buy=<?= $product["product_id"]; ?>" class="card__add-cart">+</a>
                <a href="search.php?id=<?= $product["category_id"]; ?>" class="card__category"><?= $product["category_name"]; ?></a>
                <a href="detail.php?id=<?= $product["product_id"]; ?>" class="card__title"><?= $product["product_name"]; ?></a>
                <a href="detail.php?id=<?= $product["product_id"]; ?>" class="card__price-container">
                    <div class="card__disc" style="display: <?= !empty($product["product_discount"]) ? "initial" : "none"; ?>;">
                        <?= $product["product_discount"] ? "Rp. " . number_format($product["product_discount"], 2, ",", ".") : ""; ?>
                    </div>
                    <div class="card__price">
                        Rp. <?= number_format($product["product_price"], 2, ",", "."); ?>
                    </div>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php if (count($products) != 0) : ?>
    <div class="product__link-more-container"><a href="search.php?id=<?= $products[0]["category_id"]; ?>" class="product__link-more">Lihat semua</a></div>
<?php endif; ?>