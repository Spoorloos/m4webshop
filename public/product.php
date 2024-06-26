<?php
    if (!isset($_GET['id'])) {
        exit('<h1>Missing product id! :(</h1>');
    }

    require_once 'components/database.php';

    // Fetch product information
    $connection = new DatabaseConnection();
    $result = $connection->fetch('SELECT * FROM product WHERE id = ?', 'i', $_GET['id']);
    if ($result->num_rows <= 0) {
        exit('<h1>Invalid product id! :(</h1>');
    }
    $product = $result->fetch_assoc();

    // Fetch reviews
    $results = $connection->fetch('SELECT * FROM review WHERE productid = ?', 'i', $product['id']);
    $reviews = $results->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Mick's Webshop - <?php echo $product['name'] ?></title>
    <?php require_once 'components/base_head.php' ?>
    <link rel="stylesheet" href="scss/pages/product.css">
    <script src="js/product.js" defer></script>
</head>
<body>
    <?php include_once 'components/header.php' ?>
    <main>
        <article class="product">
            <img class="product__image" src="<?php echo $product['image'] ?>" alt="product image" onclick="openModal()">
            <div class="product__info">
                <h1 class="product__info__name"><?php echo $product['name'] ?></h1>
                <strong class="product__info__price">
                    <?php if (isset($product['newprice'])): ?>
                        <span class="old-price"><?php echo number_format($product['price'], 2) ?></span>
                        <span><?php echo number_format($product['newprice'], 2) ?></span>
                    <?php else: ?>
                        <span><?php echo number_format($product['price'], 2) ?></span>
                    <?php endif; ?>
                </strong>
                <p class="product__info__description"><?php echo $product['description'] ?></p>
                <button class="product__info__addtocart black-button" type="button" onclick="addToCartButton(event, <?php echo $product['id'] ?>)">Add To Cart</button>
                <span class="product__info__dtext">Direct leverbaar | Gratis verzending vanaf €30</span>
            </div>
            <dialog class="product__image-modal">
                <img class="product__image-modal__image" src="<?php echo $product['image'] ?>" alt="product image">
                <button class="product__image-modal__close" type="button" onclick="closeModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"></path></svg>
                </button>
            </dialog>
        </article>
        <?php if (!empty($reviews)): ?>
            <h2 class="reviews__title">Reviews</h2>
            <section class="reviews">
                <?php
                    include_once 'components/review_card.php';

                    shuffle($reviews);

                    foreach (array_slice($reviews, 0, 4) as $review) {
                        render_review_card($review);
                    }
                ?>
            </section>
        <?php endif; ?>
    </main>
    <script>
        let recents = JSON.parse(Cookie.get('recents')) ?? [];
        recents.unshift(<?php echo $_GET['id'] ?>);
        recents.splice(4);
        Cookie.set('recents', JSON.stringify(recents));
    </script>
    <?php include_once 'components/footer.php' ?>
</body>
</html>