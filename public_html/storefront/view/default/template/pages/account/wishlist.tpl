<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-heart-pulse me-2"></i>
    <?php echo $heading_title; ?>
</h1>

<?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<?php if ($error) { ?>
    <div class="alert alert-error alert-danger alert-dismissible" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

	<div class="container-fluid wishlist product-list">
        <?php if ($products) {
            //add delete button
            foreach ($products as &$product) {
                $product['hide_quickview'] = true;
                $this->addHookVar(
                        'product_button_'.$product['product_id'],
                    '<a href="Javascript:void(0);" class="btn btn-danger btn-sm remove-from-list translate-middle" 
                        title="' . html2view($button_remove_wishlist) . '" data-product_id="'.$product['product_id'].'">
                        <i class="fa fa-2x fa-trash"></i></a>'
                );
            }
            ?>
            <div class="container-fluid">
                <div id="product_cell_grid">
                    <?php
                    /** @see public_html/storefront/view/default/template/blocks/product_cell_grid.tpl */
                    include( $this->templateResource('/template/blocks/product_cell_grid.tpl') ); ?>
                </div>
            </div>
        <?php } ?>
        <?php echo $this->getHookVar('more_wishlist_products'); ?>
        <div class="ps-4 p-3 col-12 d-flex flex-wrap justify-content-center">
            <?php echo $this->getHookVar('top_wishlist_buttons');
            $button_continue->style = 'btn btn-outline-secondary mx-2 mb-1';
            $button_continue->icon = 'fa fa-arrow-right';
            echo $button_continue;

            $button_cart->style = 'btn btn-success mx-2 mb-1';
            $button_cart->icon = 'fa fa-shopping-cart';
            echo $button_cart;
            echo $this->getHookVar('bottom_wishlist_buttons'); ?>
        </div>

    </div>

<script type="text/javascript">

    $(document).ready(function(){
        $('a.remove-from-list').on('click',
            function(e){
                e.preventDefault();
                let target = $(this);
                $.ajax({
                    type: 'POST',
                    url: '<?php echo $this->html->getURL('product/wishlist/remove');?>&product_id=' + target.attr('data-product_id'),
                    dataType: 'json',
                    beforeSend: function () {
                        target.hide();
                    },
                    error: function (jqXHR, exception) {
                        var text = jqXHR.statusText + ": " + jqXHR.responseText;
                        $('.alert').remove();
                        $('.wishlist').before('<div class="alert alert-error alert-danger">' + dismiss + text + '</div>');
                        target.show();
                    },
                    success: function (data) {
                        if (data.error) {
                            $('.alert').remove();
                            $('.wishlist').before('<div class="alert alert-error alert-danger">' + dismiss + data.error + '</div>');
                            target.show();
                        } else {
                            $('.wishlist .alert').remove();
                            target.parents('.product-card').fadeOut(500);
                        }
                    }
                });
        });
    });

</script>