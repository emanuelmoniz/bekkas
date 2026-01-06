<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaticTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $rows = [
            // Navigation
            ['key' => 'nav.dashboard', 'locale' => 'pt-PT', 'value' => 'Painel', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'nav.dashboard', 'locale' => 'en-UK', 'value' => 'Dashboard', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'nav.shop', 'locale' => 'pt-PT', 'value' => 'Loja', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'nav.shop', 'locale' => 'en-UK', 'value' => 'Shop', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'nav.cart', 'locale' => 'pt-PT', 'value' => 'Carrinho', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'nav.cart', 'locale' => 'en-UK', 'value' => 'Cart', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'nav.login', 'locale' => 'pt-PT', 'value' => 'Entrar', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'nav.login', 'locale' => 'en-UK', 'value' => 'Login', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'nav.register', 'locale' => 'pt-PT', 'value' => 'Registar', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'nav.register', 'locale' => 'en-UK', 'value' => 'Register', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'nav.profile', 'locale' => 'pt-PT', 'value' => 'Perfil', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'nav.profile', 'locale' => 'en-UK', 'value' => 'Profile', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'nav.logout', 'locale' => 'pt-PT', 'value' => 'Sair', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'nav.logout', 'locale' => 'en-UK', 'value' => 'Log Out', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'nav.products', 'locale' => 'pt-PT', 'value' => 'Produtos', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'nav.products', 'locale' => 'en-UK', 'value' => 'Products', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'nav.architecture', 'locale' => 'pt-PT', 'value' => 'Arquitetura', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'nav.architecture', 'locale' => 'en-UK', 'value' => 'Architecture', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'nav.about', 'locale' => 'pt-PT', 'value' => 'Sobre Nós', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'nav.about', 'locale' => 'en-UK', 'value' => 'About Us', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'nav.contact', 'locale' => 'pt-PT', 'value' => 'Contacto', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'nav.contact', 'locale' => 'en-UK', 'value' => 'Contact', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'nav.admin', 'locale' => 'pt-PT', 'value' => 'Admin', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'nav.admin', 'locale' => 'en-UK', 'value' => 'Admin', 'created_at'=>$now,'updated_at'=>$now],

            // Cart page
            ['key' => 'page.cart.title', 'locale' => 'pt-PT', 'value' => 'Carrinho', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'page.cart.title', 'locale' => 'en-UK', 'value' => 'Cart', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'cart.empty', 'locale' => 'pt-PT', 'value' => 'O seu carrinho está vazio.', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'cart.empty', 'locale' => 'en-UK', 'value' => 'Your cart is empty.', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'cart.update', 'locale' => 'pt-PT', 'value' => 'Atualizar', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'cart.update', 'locale' => 'en-UK', 'value' => 'Update', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'cart.remove', 'locale' => 'pt-PT', 'value' => 'Remover', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'cart.remove', 'locale' => 'en-UK', 'value' => 'Remove', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'cart.summary.products', 'locale' => 'pt-PT', 'value' => 'Produtos', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'cart.summary.products', 'locale' => 'en-UK', 'value' => 'Products', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'cart.summary.product_tax', 'locale' => 'pt-PT', 'value' => 'Imposto dos produtos', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'cart.summary.product_tax', 'locale' => 'en-UK', 'value' => 'Product tax', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'cart.summary.shipping', 'locale' => 'pt-PT', 'value' => 'Envio', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'cart.summary.shipping', 'locale' => 'en-UK', 'value' => 'Shipping', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'cart.summary.shipping_tax', 'locale' => 'pt-PT', 'value' => 'Imposto de envio', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'cart.summary.shipping_tax', 'locale' => 'en-UK', 'value' => 'Shipping tax', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'cart.summary.total', 'locale' => 'pt-PT', 'value' => 'Total', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'cart.summary.total', 'locale' => 'en-UK', 'value' => 'Total', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'cart.summary.total_tax', 'locale' => 'pt-PT', 'value' => 'Total imposto', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'cart.summary.total_tax', 'locale' => 'en-UK', 'value' => 'Total tax', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'cart.checkout', 'locale' => 'pt-PT', 'value' => 'Finalizar Compra', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'cart.checkout', 'locale' => 'en-UK', 'value' => 'Proceed to Checkout', 'created_at'=>$now,'updated_at'=>$now],

            // Checkout page
            ['key' => 'page.checkout.title', 'locale' => 'pt-PT', 'value' => 'Finalizar Compra', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'page.checkout.title', 'locale' => 'en-UK', 'value' => 'Checkout', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.shipping_address', 'locale' => 'pt-PT', 'value' => 'Morada de envio', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.shipping_address', 'locale' => 'en-UK', 'value' => 'Shipping Address', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.new_address', 'locale' => 'pt-PT', 'value' => 'Nova morada', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.new_address', 'locale' => 'en-UK', 'value' => 'New address', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.new_address_details', 'locale' => 'pt-PT', 'value' => 'Detalhes da nova morada', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.new_address_details', 'locale' => 'en-UK', 'value' => 'New address details', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.title', 'locale' => 'pt-PT', 'value' => 'Título', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.title', 'locale' => 'en-UK', 'value' => 'Title', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.nif', 'locale' => 'pt-PT', 'value' => 'NIF', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.nif', 'locale' => 'en-UK', 'value' => 'NIF', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.address_line_1', 'locale' => 'pt-PT', 'value' => 'Morada linha 1', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.address_line_1', 'locale' => 'en-UK', 'value' => 'Address line 1', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.address_line_2', 'locale' => 'pt-PT', 'value' => 'Morada linha 2', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.address_line_2', 'locale' => 'en-UK', 'value' => 'Address line 2', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.postal_code', 'locale' => 'pt-PT', 'value' => 'Código postal', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.postal_code', 'locale' => 'en-UK', 'value' => 'Postal code', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.city', 'locale' => 'pt-PT', 'value' => 'Cidade', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.city', 'locale' => 'en-UK', 'value' => 'City', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.country', 'locale' => 'pt-PT', 'value' => 'País', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.country', 'locale' => 'en-UK', 'value' => 'Country', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.place_order', 'locale' => 'pt-PT', 'value' => 'Finalizar encomenda', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.place_order', 'locale' => 'en-UK', 'value' => 'Place Order', 'created_at'=>$now,'updated_at'=>$now],

            // Orders page
            ['key' => 'orders.status', 'locale' => 'pt-PT', 'value' => 'Estado', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.status', 'locale' => 'en-UK', 'value' => 'Status', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.paid', 'locale' => 'pt-PT', 'value' => 'Pago', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.paid', 'locale' => 'en-UK', 'value' => 'Paid', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.canceled', 'locale' => 'pt-PT', 'value' => 'Cancelado', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.canceled', 'locale' => 'en-UK', 'value' => 'Canceled', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.refunded', 'locale' => 'pt-PT', 'value' => 'Reembolsado', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.refunded', 'locale' => 'en-UK', 'value' => 'Refunded', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.tracking', 'locale' => 'pt-PT', 'value' => 'Rastreamento', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.tracking', 'locale' => 'en-UK', 'value' => 'Tracking', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.date', 'locale' => 'pt-PT', 'value' => 'Data', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.date', 'locale' => 'en-UK', 'value' => 'Date', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.not_paid', 'locale' => 'pt-PT', 'value' => 'Não', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.not_paid', 'locale' => 'en-UK', 'value' => 'No', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.shipping_address', 'locale' => 'pt-PT', 'value' => 'Morada de envio', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.shipping_address', 'locale' => 'en-UK', 'value' => 'Shipping Address', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.nif', 'locale' => 'pt-PT', 'value' => 'NIF', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.nif', 'locale' => 'en-UK', 'value' => 'NIF', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.products', 'locale' => 'pt-PT', 'value' => 'Produtos', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.products', 'locale' => 'en-UK', 'value' => 'Products', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.product', 'locale' => 'pt-PT', 'value' => 'Produto', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.product', 'locale' => 'en-UK', 'value' => 'Product', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.qty', 'locale' => 'pt-PT', 'value' => 'Qtd', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.qty', 'locale' => 'en-UK', 'value' => 'Qty', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.gross', 'locale' => 'pt-PT', 'value' => 'Bruto', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.gross', 'locale' => 'en-UK', 'value' => 'Gross', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.products_net', 'locale' => 'pt-PT', 'value' => 'Produtos (líquido)', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.products_net', 'locale' => 'en-UK', 'value' => 'Products (net)', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.products_tax', 'locale' => 'pt-PT', 'value' => 'Imposto dos produtos', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.products_tax', 'locale' => 'en-UK', 'value' => 'Products tax', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.shipping', 'locale' => 'pt-PT', 'value' => 'Envio', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.shipping', 'locale' => 'en-UK', 'value' => 'Shipping', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.shipping_tax', 'locale' => 'pt-PT', 'value' => 'Imposto de envio', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.shipping_tax', 'locale' => 'en-UK', 'value' => 'Shipping tax', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.total', 'locale' => 'pt-PT', 'value' => 'Total', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.total', 'locale' => 'en-UK', 'value' => 'Total', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.my_orders', 'locale' => 'pt-PT', 'value' => 'As Minhas Encomendas', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.my_orders', 'locale' => 'en-UK', 'value' => 'My Orders', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.order_number', 'locale' => 'pt-PT', 'value' => 'Encomenda #', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.order_number', 'locale' => 'en-UK', 'value' => 'Order #', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.view', 'locale' => 'pt-PT', 'value' => 'Ver', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.view', 'locale' => 'en-UK', 'value' => 'View', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'orders.no_orders', 'locale' => 'pt-PT', 'value' => 'Nenhuma encomenda encontrada.', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'orders.no_orders', 'locale' => 'en-UK', 'value' => 'No orders found.', 'created_at'=>$now,'updated_at'=>$now],

            // Products page
            ['key' => 'products.title', 'locale' => 'pt-PT', 'value' => 'Produtos', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.title', 'locale' => 'en-UK', 'value' => 'Products', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.name', 'locale' => 'pt-PT', 'value' => 'Nome', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.name', 'locale' => 'en-UK', 'value' => 'Name', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.category', 'locale' => 'pt-PT', 'value' => 'Categoria', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.category', 'locale' => 'en-UK', 'value' => 'Category', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.material', 'locale' => 'pt-PT', 'value' => 'Material', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.material', 'locale' => 'en-UK', 'value' => 'Material', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.search', 'locale' => 'pt-PT', 'value' => 'Pesquisar...', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.search', 'locale' => 'en-UK', 'value' => 'Search...', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.new', 'locale' => 'pt-PT', 'value' => 'Novo', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.new', 'locale' => 'en-UK', 'value' => 'New', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.only_new', 'locale' => 'pt-PT', 'value' => 'Apenas novos', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.only_new', 'locale' => 'en-UK', 'value' => 'Only New', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.not_new', 'locale' => 'pt-PT', 'value' => 'Não novos', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.not_new', 'locale' => 'en-UK', 'value' => 'Not New', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.promo', 'locale' => 'pt-PT', 'value' => 'Promoção', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.promo', 'locale' => 'en-UK', 'value' => 'Promo', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.only_promo', 'locale' => 'pt-PT', 'value' => 'Apenas promoções', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.only_promo', 'locale' => 'en-UK', 'value' => 'Only Promo', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.not_promo', 'locale' => 'pt-PT', 'value' => 'Não em promoção', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.not_promo', 'locale' => 'en-UK', 'value' => 'Not Promo', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.in_stock', 'locale' => 'pt-PT', 'value' => 'Em stock', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.in_stock', 'locale' => 'en-UK', 'value' => 'In stock', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.apply', 'locale' => 'pt-PT', 'value' => 'Filtrar', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.apply', 'locale' => 'en-UK', 'value' => 'Filter', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.filter.reset', 'locale' => 'pt-PT', 'value' => 'Limpar', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.filter.reset', 'locale' => 'en-UK', 'value' => 'Reset', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.no_products', 'locale' => 'pt-PT', 'value' => 'Nenhum produto encontrado.', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.no_products', 'locale' => 'en-UK', 'value' => 'No products found.', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.no_photos', 'locale' => 'pt-PT', 'value' => 'Sem fotos disponíveis', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.no_photos', 'locale' => 'en-UK', 'value' => 'No photos available', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.weight', 'locale' => 'pt-PT', 'value' => 'Peso', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.weight', 'locale' => 'en-UK', 'value' => 'Weight', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.stock', 'locale' => 'pt-PT', 'value' => 'Stock', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.stock', 'locale' => 'en-UK', 'value' => 'Stock', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.available', 'locale' => 'pt-PT', 'value' => 'Disponível', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.available', 'locale' => 'en-UK', 'value' => 'Available', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.out_of_stock', 'locale' => 'pt-PT', 'value' => 'Esgotado', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.out_of_stock', 'locale' => 'en-UK', 'value' => 'Out of stock', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.categories', 'locale' => 'pt-PT', 'value' => 'Categorias', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.categories', 'locale' => 'en-UK', 'value' => 'Categories', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.materials', 'locale' => 'pt-PT', 'value' => 'Materiais', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.materials', 'locale' => 'en-UK', 'value' => 'Materials', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'products.add_to_cart', 'locale' => 'pt-PT', 'value' => 'Adicionar ao carrinho', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'products.add_to_cart', 'locale' => 'en-UK', 'value' => 'Add to cart', 'created_at'=>$now,'updated_at'=>$now],

            // Checkout additional
            ['key' => 'checkout.title', 'locale' => 'pt-PT', 'value' => 'Finalizar Compra', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.title', 'locale' => 'en-UK', 'value' => 'Checkout', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.summary', 'locale' => 'pt-PT', 'value' => 'Resumo', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.summary', 'locale' => 'en-UK', 'value' => 'Summary', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.shipping', 'locale' => 'pt-PT', 'value' => 'Envio', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.shipping', 'locale' => 'en-UK', 'value' => 'Shipping', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.products_tax', 'locale' => 'pt-PT', 'value' => 'Imposto dos produtos', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.products_tax', 'locale' => 'en-UK', 'value' => 'Products tax', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.shipping_tax', 'locale' => 'pt-PT', 'value' => 'Imposto de envio', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.shipping_tax', 'locale' => 'en-UK', 'value' => 'Shipping tax', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.total_tax', 'locale' => 'pt-PT', 'value' => 'Total imposto', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.total_tax', 'locale' => 'en-UK', 'value' => 'Total tax', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'checkout.total', 'locale' => 'pt-PT', 'value' => 'Total', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'checkout.total', 'locale' => 'en-UK', 'value' => 'Total', 'created_at'=>$now,'updated_at'=>$now],

            // Profile
            ['key' => 'profile.addresses', 'locale' => 'pt-PT', 'value' => 'Moradas', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.addresses', 'locale' => 'en-UK', 'value' => 'Addresses', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.default_address', 'locale' => 'pt-PT', 'value' => 'Morada predefinida', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.default_address', 'locale' => 'en-UK', 'value' => 'Default address', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.save', 'locale' => 'pt-PT', 'value' => 'Guardar', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.save', 'locale' => 'en-UK', 'value' => 'Save', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.delete', 'locale' => 'pt-PT', 'value' => 'Eliminar', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.delete', 'locale' => 'en-UK', 'value' => 'Delete', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.no_addresses', 'locale' => 'pt-PT', 'value' => 'Ainda sem moradas.', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.no_addresses', 'locale' => 'en-UK', 'value' => 'No addresses yet.', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.add_new_address', 'locale' => 'pt-PT', 'value' => 'Adicionar nova morada', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.add_new_address', 'locale' => 'en-UK', 'value' => 'Add new address', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.address_title', 'locale' => 'pt-PT', 'value' => 'Título', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.address_title', 'locale' => 'en-UK', 'value' => 'Title', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.address_nif', 'locale' => 'pt-PT', 'value' => 'NIF', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.address_nif', 'locale' => 'en-UK', 'value' => 'NIF', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.address_line_1', 'locale' => 'pt-PT', 'value' => 'Morada linha 1', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.address_line_1', 'locale' => 'en-UK', 'value' => 'Address line 1', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.address_line_2', 'locale' => 'pt-PT', 'value' => 'Morada linha 2', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.address_line_2', 'locale' => 'en-UK', 'value' => 'Address line 2', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.address_postal_code', 'locale' => 'pt-PT', 'value' => 'Código postal', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.address_postal_code', 'locale' => 'en-UK', 'value' => 'Postal code', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.address_city', 'locale' => 'pt-PT', 'value' => 'Cidade', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.address_city', 'locale' => 'en-UK', 'value' => 'City', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.address_country', 'locale' => 'pt-PT', 'value' => 'País', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.address_country', 'locale' => 'en-UK', 'value' => 'Country', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.add_address', 'locale' => 'pt-PT', 'value' => 'Adicionar morada', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.add_address', 'locale' => 'en-UK', 'value' => 'Add Address', 'created_at'=>$now,'updated_at'=>$now],

            // Profile forms
            ['key' => 'profile.profile_information', 'locale' => 'pt-PT', 'value' => 'Informação do Perfil', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.profile_information', 'locale' => 'en-UK', 'value' => 'Profile Information', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.update_profile_info_desc', 'locale' => 'pt-PT', 'value' => 'Atualize as informações do perfil e endereço de email da sua conta.', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.update_profile_info_desc', 'locale' => 'en-UK', 'value' => "Update your account's profile information and email address.", 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.email_unverified', 'locale' => 'pt-PT', 'value' => 'O seu endereço de email não está verificado.', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.email_unverified', 'locale' => 'en-UK', 'value' => 'Your email address is unverified.', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.resend_verification', 'locale' => 'pt-PT', 'value' => 'Clique aqui para reenviar o email de verificação.', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.resend_verification', 'locale' => 'en-UK', 'value' => 'Click here to re-send the verification email.', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.verification_sent', 'locale' => 'pt-PT', 'value' => 'Um novo link de verificação foi enviado para o seu endereço de email.', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.verification_sent', 'locale' => 'en-UK', 'value' => 'A new verification link has been sent to your email address.', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.update_password', 'locale' => 'pt-PT', 'value' => 'Atualizar Palavra-passe', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.update_password', 'locale' => 'en-UK', 'value' => 'Update Password', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.update_password_desc', 'locale' => 'pt-PT', 'value' => 'Certifique-se de que a sua conta está a usar uma palavra-passe longa e aleatória para se manter seguro.', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.update_password_desc', 'locale' => 'en-UK', 'value' => 'Ensure your account is using a long, random password to stay secure.', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.delete_account', 'locale' => 'pt-PT', 'value' => 'Eliminar Conta', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.delete_account', 'locale' => 'en-UK', 'value' => 'Delete Account', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.delete_account_desc', 'locale' => 'pt-PT', 'value' => 'Uma vez que a sua conta é eliminada, todos os seus recursos e dados serão permanentemente eliminados. Antes de eliminar a sua conta, por favor faça o download de quaisquer dados ou informações que deseja reter.', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.delete_account_desc', 'locale' => 'en-UK', 'value' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.confirm_delete', 'locale' => 'pt-PT', 'value' => 'Tem a certeza de que deseja eliminar a sua conta?', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.confirm_delete', 'locale' => 'en-UK', 'value' => 'Are you sure you want to delete your account?', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'profile.confirm_delete_desc', 'locale' => 'pt-PT', 'value' => 'Uma vez que a sua conta é eliminada, todos os seus recursos e dados serão permanentemente eliminados. Por favor, insira a sua palavra-passe para confirmar que deseja eliminar permanentemente a sua conta.', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'profile.confirm_delete_desc', 'locale' => 'en-UK', 'value' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.', 'created_at'=>$now,'updated_at'=>$now],

            // Auth forms
            ['key' => 'auth.remember_me', 'locale' => 'pt-PT', 'value' => 'Lembrar-me', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'auth.remember_me', 'locale' => 'en-UK', 'value' => 'Remember me', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'auth.forgot_password', 'locale' => 'pt-PT', 'value' => 'Esqueceu a sua palavra-passe?', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'auth.forgot_password', 'locale' => 'en-UK', 'value' => 'Forgot your password?', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'auth.login', 'locale' => 'pt-PT', 'value' => 'Entrar', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'auth.login', 'locale' => 'en-UK', 'value' => 'Log in', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'auth.already_registered', 'locale' => 'pt-PT', 'value' => 'Já está registado?', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'auth.already_registered', 'locale' => 'en-UK', 'value' => 'Already registered?', 'created_at'=>$now,'updated_at'=>$now],

            ['key' => 'auth.register', 'locale' => 'pt-PT', 'value' => 'Registar', 'created_at'=>$now,'updated_at'=>$now],
            ['key' => 'auth.register', 'locale' => 'en-UK', 'value' => 'Register', 'created_at'=>$now,'updated_at'=>$now],
        ];

        DB::table('static_translations')->upsert($rows, ['key','locale']);
    }
}
