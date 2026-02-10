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
            // Profile language labels
            ['key' => 'profile.language', 'locale' => 'pt-PT', 'value' => 'Idioma', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.language', 'locale' => 'en-UK', 'value' => 'Language', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.save', 'locale' => 'pt-PT', 'value' => 'Guardar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.save', 'locale' => 'en-UK', 'value' => 'Save', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.saved', 'locale' => 'pt-PT', 'value' => 'Guardado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.saved', 'locale' => 'en-UK', 'value' => 'Saved.', 'created_at' => $now, 'updated_at' => $now],
            // Orders Yes/No
            ['key' => 'orders.yes', 'locale' => 'pt-PT', 'value' => 'Sim', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.yes', 'locale' => 'en-UK', 'value' => 'Yes', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.no', 'locale' => 'pt-PT', 'value' => 'Não', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.no', 'locale' => 'en-UK', 'value' => 'No', 'created_at' => $now, 'updated_at' => $now],
            // Navigation
            ['key' => 'nav.dashboard', 'locale' => 'pt-PT', 'value' => 'Painel', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.dashboard', 'locale' => 'en-UK', 'value' => 'Dashboard', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.shop', 'locale' => 'pt-PT', 'value' => 'Loja', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.shop', 'locale' => 'en-UK', 'value' => 'Shop', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.cart', 'locale' => 'pt-PT', 'value' => 'Carrinho', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.cart', 'locale' => 'en-UK', 'value' => 'Cart', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.login', 'locale' => 'pt-PT', 'value' => 'Entrar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.login', 'locale' => 'en-UK', 'value' => 'Login', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.register', 'locale' => 'pt-PT', 'value' => 'Registar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.register', 'locale' => 'en-UK', 'value' => 'Register', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.profile', 'locale' => 'pt-PT', 'value' => 'Perfil', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.profile', 'locale' => 'en-UK', 'value' => 'Profile', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.logout', 'locale' => 'pt-PT', 'value' => 'Sair', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.logout', 'locale' => 'en-UK', 'value' => 'Log Out', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.products', 'locale' => 'pt-PT', 'value' => 'Produtos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.products', 'locale' => 'en-UK', 'value' => 'Products', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.architecture', 'locale' => 'pt-PT', 'value' => 'Arquitetura', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.architecture', 'locale' => 'en-UK', 'value' => 'Architecture', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.about', 'locale' => 'pt-PT', 'value' => 'Sobre Nós', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.about', 'locale' => 'en-UK', 'value' => 'About Us', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.contact', 'locale' => 'pt-PT', 'value' => 'Contacto', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.contact', 'locale' => 'en-UK', 'value' => 'Contact', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.admin', 'locale' => 'pt-PT', 'value' => 'Admin', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.admin', 'locale' => 'en-UK', 'value' => 'Admin', 'created_at' => $now, 'updated_at' => $now],

            // Cart page
            ['key' => 'page.cart.title', 'locale' => 'pt-PT', 'value' => 'Carrinho', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'page.cart.title', 'locale' => 'en-UK', 'value' => 'Cart', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.empty', 'locale' => 'pt-PT', 'value' => 'O seu carrinho está vazio.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.empty', 'locale' => 'en-UK', 'value' => 'Your cart is empty.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.update', 'locale' => 'pt-PT', 'value' => 'Atualizar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.update', 'locale' => 'en-UK', 'value' => 'Update', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.remove', 'locale' => 'pt-PT', 'value' => 'Remover', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.remove', 'locale' => 'en-UK', 'value' => 'Remove', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.summary.products', 'locale' => 'pt-PT', 'value' => 'Produtos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.summary.products', 'locale' => 'en-UK', 'value' => 'Products', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.summary.product_tax', 'locale' => 'pt-PT', 'value' => 'Imposto dos produtos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.summary.product_tax', 'locale' => 'en-UK', 'value' => 'Product tax', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.summary.shipping', 'locale' => 'pt-PT', 'value' => 'Envio', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.summary.shipping', 'locale' => 'en-UK', 'value' => 'Shipping', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.summary.shipping_tax', 'locale' => 'pt-PT', 'value' => 'Imposto de envio', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.summary.shipping_tax', 'locale' => 'en-UK', 'value' => 'Shipping tax', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.summary.total', 'locale' => 'pt-PT', 'value' => 'Total', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.summary.total', 'locale' => 'en-UK', 'value' => 'Total', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.summary.total_tax', 'locale' => 'pt-PT', 'value' => 'Total imposto', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.summary.total_tax', 'locale' => 'en-UK', 'value' => 'Total tax', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.checkout', 'locale' => 'pt-PT', 'value' => 'Finalizar Compra', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.checkout', 'locale' => 'en-UK', 'value' => 'Proceed to Checkout', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.continue_shopping', 'locale' => 'pt-PT', 'value' => 'Continuar a Comprar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.continue_shopping', 'locale' => 'en-UK', 'value' => 'Continue Shopping', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.start_shopping', 'locale' => 'pt-PT', 'value' => 'Começar a Comprar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.start_shopping', 'locale' => 'en-UK', 'value' => 'Start Shopping', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.summary.subtotal', 'locale' => 'pt-PT', 'value' => 'Subtotal', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.summary.subtotal', 'locale' => 'en-UK', 'value' => 'Subtotal', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'cart.shipping_at_checkout', 'locale' => 'pt-PT', 'value' => 'O custo de envio será calculado no checkout.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cart.shipping_at_checkout', 'locale' => 'en-UK', 'value' => 'The shipping cost will be calculated at checkout.', 'created_at' => $now, 'updated_at' => $now],

            // Shipping Config (Admin)
            ['key' => 'shipping_config.title', 'locale' => 'pt-PT', 'value' => 'Configuração de Envio', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'shipping_config.title', 'locale' => 'en-UK', 'value' => 'Shipping Configuration', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'shipping_config.free_shipping_over', 'locale' => 'pt-PT', 'value' => 'Envio Gratuito Acima de (€)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'shipping_config.free_shipping_over', 'locale' => 'en-UK', 'value' => 'Free Shipping Over (€)', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'shipping_config.free_shipping_help', 'locale' => 'pt-PT', 'value' => 'Defina como 0 para desativar o limite de envio gratuito', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'shipping_config.free_shipping_help', 'locale' => 'en-UK', 'value' => 'Set to 0 to disable free shipping threshold', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'shipping_config.success', 'locale' => 'pt-PT', 'value' => 'Configuração de envio atualizada com sucesso.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'shipping_config.success', 'locale' => 'en-UK', 'value' => 'Shipping configuration updated successfully.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'shipping_config.save_changes', 'locale' => 'pt-PT', 'value' => 'Guardar Alterações', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'shipping_config.save_changes', 'locale' => 'en-UK', 'value' => 'Save Changes', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'shipping_config.cancel', 'locale' => 'pt-PT', 'value' => 'Cancelar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'shipping_config.cancel', 'locale' => 'en-UK', 'value' => 'Cancel', 'created_at' => $now, 'updated_at' => $now],

            // Favorites
            ['key' => 'nav.favorites', 'locale' => 'pt-PT', 'value' => 'Favoritos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.favorites', 'locale' => 'en-UK', 'value' => 'Favorites', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.title', 'locale' => 'pt-PT', 'value' => 'Os Meus Favoritos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.title', 'locale' => 'en-UK', 'value' => 'My Favorites', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.empty', 'locale' => 'pt-PT', 'value' => 'Não tem produtos favoritos.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.empty', 'locale' => 'en-UK', 'value' => 'You have no favorite products.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.no_results', 'locale' => 'pt-PT', 'value' => 'Nenhum favorito corresponde aos critérios do filtro.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.no_results', 'locale' => 'en-UK', 'value' => 'No favorites match your filter criteria.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.browse_products', 'locale' => 'pt-PT', 'value' => 'Explorar Produtos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.browse_products', 'locale' => 'en-UK', 'value' => 'Browse Products', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.remove', 'locale' => 'pt-PT', 'value' => 'Remover', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.remove', 'locale' => 'en-UK', 'value' => 'Remove', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.filters', 'locale' => 'pt-PT', 'value' => 'Filtros', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.filters', 'locale' => 'en-UK', 'value' => 'Filters', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.filter.search', 'locale' => 'pt-PT', 'value' => 'Pesquisar...', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.filter.search', 'locale' => 'en-UK', 'value' => 'Search...', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.filter.all_categories', 'locale' => 'pt-PT', 'value' => 'Todas as Categorias', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.filter.all_categories', 'locale' => 'en-UK', 'value' => 'All Categories', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.filter.all_materials', 'locale' => 'pt-PT', 'value' => 'Todos os Materiais', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.filter.all_materials', 'locale' => 'en-UK', 'value' => 'All Materials', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.filter.min_price', 'locale' => 'pt-PT', 'value' => 'Mín €', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.filter.min_price', 'locale' => 'en-UK', 'value' => 'Min €', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.filter.max_price', 'locale' => 'pt-PT', 'value' => 'Máx €', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.filter.max_price', 'locale' => 'en-UK', 'value' => 'Max €', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.filter.apply', 'locale' => 'pt-PT', 'value' => 'Filtrar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.filter.apply', 'locale' => 'en-UK', 'value' => 'Filter', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.filter.reset', 'locale' => 'pt-PT', 'value' => 'Limpar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.filter.reset', 'locale' => 'en-UK', 'value' => 'Reset', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'favorites.removed', 'locale' => 'pt-PT', 'value' => 'Produto removido dos favoritos.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favorites.removed', 'locale' => 'en-UK', 'value' => 'Product removed from favorites.', 'created_at' => $now, 'updated_at' => $now],

            // Checkout page
            ['key' => 'page.checkout.title', 'locale' => 'pt-PT', 'value' => 'Finalizar Compra', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'page.checkout.title', 'locale' => 'en-UK', 'value' => 'Checkout', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.shipping_address', 'locale' => 'pt-PT', 'value' => 'Morada de envio', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.shipping_address', 'locale' => 'en-UK', 'value' => 'Shipping Address', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.new_address', 'locale' => 'pt-PT', 'value' => 'Nova morada', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.new_address', 'locale' => 'en-UK', 'value' => 'New address', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.new_address_details', 'locale' => 'pt-PT', 'value' => 'Detalhes da nova morada', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.new_address_details', 'locale' => 'en-UK', 'value' => 'New address details', 'created_at' => $now, 'updated_at' => $now],

            // Easypay / Pay page messages
            ['key' => 'checkout.pay.loading_widget', 'locale' => 'pt-PT', 'value' => 'A carregar o componente de pagamento…', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.loading_widget', 'locale' => 'en-UK', 'value' => 'Loading payment widget…', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.pay.unavailable', 'locale' => 'pt-PT', 'value' => 'O sistema de pagamentos está temporariamente indisponível — verifique os detalhes da encomenda dentro de momentos e tente novamente.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.unavailable', 'locale' => 'en-UK', 'value' => 'Payment system is temporarily unavailable — please check your order details in a moment and try again.', 'created_at' => $now, 'updated_at' => $now],

            // Shown only when APP_DEBUG=true (appends error details)
            ['key' => 'checkout.pay.unavailable_debug', 'locale' => 'pt-PT', 'value' => 'Erro: :error', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.unavailable_debug', 'locale' => 'en-UK', 'value' => 'Error: :error', 'created_at' => $now, 'updated_at' => $now],

            // Additional Easypay messages
            ['key' => 'checkout.pay.success', 'locale' => 'pt-PT', 'value' => 'Pagamento recebido — obrigado. A atualizar o estado da encomenda…', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.success', 'locale' => 'en-UK', 'value' => 'Payment received — thank you. Updating order status…', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.pay.widget_failed', 'locale' => 'pt-PT', 'value' => 'O componente de pagamento falhou ao carregar — por favor tente novamente.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.widget_failed', 'locale' => 'en-UK', 'value' => 'Payment widget failed to load — please try again.', 'created_at' => $now, 'updated_at' => $now],

            // Messages for SDK pre-checks / error-handling flows
            ['key' => 'checkout.pay.already_paid', 'locale' => 'pt-PT', 'value' => 'A encomenda já foi paga. Obrigado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.already_paid', 'locale' => 'en-UK', 'value' => 'This order has already been paid. Thank you.', 'created_at' => $now, 'updated_at' => $now],

            // When SDK reports already-paid but remote does not confirm any paid payment for the order
            ['key' => 'checkout.pay.error_contact_support', 'locale' => 'pt-PT', 'value' => 'Ocorreu um problema com o seu pagamento — por favor contacte o suporte.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.error_contact_support', 'locale' => 'en-UK', 'value' => 'There is something in error with your payment, please contact support.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.pay.new_session_created', 'locale' => 'pt-PT', 'value' => 'Criado um novo pedido de pagamento — por favor selecione o método e tente novamente.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.new_session_created', 'locale' => 'en-UK', 'value' => 'Created a new payment session — please select a method and try again.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.pay.cannot_start_sdk_paid', 'locale' => 'pt-PT', 'value' => 'A SDK não será iniciada porque a encomenda já está paga.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.cannot_start_sdk_paid', 'locale' => 'en-UK', 'value' => 'SDK will not start because the order is already paid.', 'created_at' => $now, 'updated_at' => $now],

            // New: client-facing payment-status messages shown on the pay page when a persisted
            // payment exists and is authoritative (refreshed from Easypay single-payment endpoint).
            ['key' => 'checkout.pay.status.paid', 'locale' => 'pt-PT', 'value' => 'Pagamento concluído — a sua encomenda está a ser processada.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.status.paid', 'locale' => 'en-UK', 'value' => 'Payment completed — your order is being processed.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.pay.status.authorised', 'locale' => 'pt-PT', 'value' => 'Pagamento autorizado — o processamento está em curso, verifique os detalhes da encomenda dentro de momentos.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.status.authorised', 'locale' => 'en-UK', 'value' => 'Payment authorised — processing is underway, please check your order details in a moment.', 'created_at' => $now, 'updated_at' => $now],

            // Message returned by the SDK onSuccess when payment is created but still pending (MB/IBAN/etc.)
            ['key' => 'checkout.pay.on_success.pending', 'locale' => 'pt-PT', 'value' => 'Informação de pagamento criada — siga as instruções fornecidas para efetuar o pagamento; a encomenda será processada posteriormente.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.on_success.pending', 'locale' => 'en-UK', 'value' => 'Payment info created — please follow the provided instructions to complete payment; your order will be processed afterwards.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.pay.payment_info_title', 'locale' => 'pt-PT', 'value' => 'Informação de pagamento', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.payment_info_title', 'locale' => 'en-UK', 'value' => 'Payment information', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.pay.mb_entity', 'locale' => 'pt-PT', 'value' => 'Entidade MB', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.mb_entity', 'locale' => 'en-UK', 'value' => 'MB entity', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.pay.mb_reference', 'locale' => 'pt-PT', 'value' => 'Referência MB', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.mb_reference', 'locale' => 'en-UK', 'value' => 'MB reference', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.pay.mb_expires', 'locale' => 'pt-PT', 'value' => 'Expira em', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.mb_expires', 'locale' => 'en-UK', 'value' => 'Expires at', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.pay.iban', 'locale' => 'pt-PT', 'value' => 'IBAN', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.iban', 'locale' => 'en-UK', 'value' => 'IBAN', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.pay.session_cancelled', 'locale' => 'pt-PT', 'value' => 'Sessão de pagamento anterior cancelada. A criar nova sessão…', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.pay.session_cancelled', 'locale' => 'en-UK', 'value' => 'Previous payment session cancelled. Creating a new session…', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.address_title', 'locale' => 'pt-PT', 'value' => 'Nome da morada', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.address_title', 'locale' => 'en-UK', 'value' => 'Address name', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.nif', 'locale' => 'pt-PT', 'value' => 'NIF', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.nif', 'locale' => 'en-UK', 'value' => 'NIF', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.address_line_1', 'locale' => 'pt-PT', 'value' => 'Morada linha 1', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.address_line_1', 'locale' => 'en-UK', 'value' => 'Address line 1', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.address_line_2', 'locale' => 'pt-PT', 'value' => 'Morada linha 2', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.address_line_2', 'locale' => 'en-UK', 'value' => 'Address line 2', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.postal_code', 'locale' => 'pt-PT', 'value' => 'Código postal', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.postal_code', 'locale' => 'en-UK', 'value' => 'Postal code', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.city', 'locale' => 'pt-PT', 'value' => 'Cidade', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.city', 'locale' => 'en-UK', 'value' => 'City', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.country', 'locale' => 'pt-PT', 'value' => 'País', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.country', 'locale' => 'en-UK', 'value' => 'Country', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.set_as_default', 'locale' => 'pt-PT', 'value' => 'Definir como morada predefinida', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.set_as_default', 'locale' => 'en-UK', 'value' => 'Set as default address', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.place_order', 'locale' => 'pt-PT', 'value' => 'Finalizar encomenda', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.place_order', 'locale' => 'en-UK', 'value' => 'Place Order', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.free_shipping_qualified', 'locale' => 'pt-PT', 'value' => 'Qualificou-se para envio grátis! Pode também escolher um método de envio mais rápido abaixo (custo adicional aplica-se).', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.free_shipping_qualified', 'locale' => 'en-UK', 'value' => 'You qualify for free shipping! You can also choose a faster shipping method below (additional cost applies).', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.free', 'locale' => 'pt-PT', 'value' => 'GRÁTIS', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.free', 'locale' => 'en-UK', 'value' => 'FREE', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.free_shipping_message', 'locale' => 'pt-PT', 'value' => 'O total da sua encomenda excede', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.free_shipping_message', 'locale' => 'en-UK', 'value' => 'Your order total exceeds', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.free_shipping_applied', 'locale' => 'pt-PT', 'value' => 'Envio grátis foi aplicado à sua encomenda.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.free_shipping_applied', 'locale' => 'en-UK', 'value' => 'Free shipping has been applied to your order.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.shipping_method', 'locale' => 'pt-PT', 'value' => 'Método de envio', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.shipping_method', 'locale' => 'en-UK', 'value' => 'Shipping method', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.address_required_for_shipping', 'locale' => 'pt-PT', 'value' => 'Por favor, preencha o formulário de morada para podermos mostrar as opções de envio disponíveis.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.address_required_for_shipping', 'locale' => 'en-UK', 'value' => 'Please fill the address form so we can show the available shipping options.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.select_shipping_method', 'locale' => 'pt-PT', 'value' => 'Selecione o Método de Envio', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.select_shipping_method', 'locale' => 'en-UK', 'value' => 'Select Shipping Method', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.no_shipping_available', 'locale' => 'pt-PT', 'value' => 'Não há opções de envio disponíveis para a sua morada. Por favor, contacte-nos.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.no_shipping_available', 'locale' => 'en-UK', 'value' => 'No shipping options available for your address. Please contact us.', 'created_at' => $now, 'updated_at' => $now],

            // Orders page
            ['key' => 'orders.status', 'locale' => 'pt-PT', 'value' => 'Estado', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.status', 'locale' => 'en-UK', 'value' => 'Status', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.paid', 'locale' => 'pt-PT', 'value' => 'Pago', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.paid', 'locale' => 'en-UK', 'value' => 'Paid', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.canceled', 'locale' => 'pt-PT', 'value' => 'Cancelado', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.canceled', 'locale' => 'en-UK', 'value' => 'Canceled', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.refunded', 'locale' => 'pt-PT', 'value' => 'Reembolsado', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.refunded', 'locale' => 'en-UK', 'value' => 'Refunded', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.tracking', 'locale' => 'pt-PT', 'value' => 'Rastreamento', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.tracking', 'locale' => 'en-UK', 'value' => 'Tracking', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.date', 'locale' => 'pt-PT', 'value' => 'Data', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.date', 'locale' => 'en-UK', 'value' => 'Date', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.last_update', 'locale' => 'pt-PT', 'value' => 'Última Atualização', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.last_update', 'locale' => 'en-UK', 'value' => 'Last Update', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.not_paid', 'locale' => 'pt-PT', 'value' => 'Não', 'created_at' => $now, 'updated_at' => $now],

            // Flash shown after successful order placement (client-facing)
            ['key' => 'orders.placed_success', 'locale' => 'pt-PT', 'value' => 'Encomenda efetuada com sucesso! Obrigado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.placed_success', 'locale' => 'en-UK', 'value' => 'Order placed successfully! Thank you.', 'created_at' => $now, 'updated_at' => $now],

            // Pay now button (client-facing)
            ['key' => 'orders.pay_now', 'locale' => 'pt-PT', 'value' => 'Pagar agora', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.pay_now', 'locale' => 'en-UK', 'value' => 'Pay now', 'created_at' => $now, 'updated_at' => $now],

            // Shown on the order details page when a persisted payment exists (pending) — links to the pay page
            ['key' => 'orders.change_payment', 'locale' => 'pt-PT', 'value' => 'Alterar pagamento', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.change_payment', 'locale' => 'en-UK', 'value' => 'Change payment', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.not_paid', 'locale' => 'en-UK', 'value' => 'No', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.shipping_address', 'locale' => 'pt-PT', 'value' => 'Morada de envio', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.shipping_address', 'locale' => 'en-UK', 'value' => 'Shipping Address', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.nif', 'locale' => 'pt-PT', 'value' => 'NIF', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.nif', 'locale' => 'en-UK', 'value' => 'NIF', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.products', 'locale' => 'pt-PT', 'value' => 'Produtos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.products', 'locale' => 'en-UK', 'value' => 'Products', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.product', 'locale' => 'pt-PT', 'value' => 'Produto', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.product', 'locale' => 'en-UK', 'value' => 'Product', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.qty', 'locale' => 'pt-PT', 'value' => 'Qtd', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.qty', 'locale' => 'en-UK', 'value' => 'Qty', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.gross', 'locale' => 'pt-PT', 'value' => 'Bruto', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.gross', 'locale' => 'en-UK', 'value' => 'Gross', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.products_net', 'locale' => 'pt-PT', 'value' => 'Produtos (líquido)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.products_net', 'locale' => 'en-UK', 'value' => 'Products (net)', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.products_tax', 'locale' => 'pt-PT', 'value' => 'Imposto dos produtos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.products_tax', 'locale' => 'en-UK', 'value' => 'Products tax', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.shipping', 'locale' => 'pt-PT', 'value' => 'Envio', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.shipping', 'locale' => 'en-UK', 'value' => 'Shipping', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.shipping_tax', 'locale' => 'pt-PT', 'value' => 'Imposto de envio', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.shipping_tax', 'locale' => 'en-UK', 'value' => 'Shipping tax', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.total', 'locale' => 'pt-PT', 'value' => 'Total', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.total', 'locale' => 'en-UK', 'value' => 'Total', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.my_orders', 'locale' => 'pt-PT', 'value' => 'As Minhas Encomendas', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.my_orders', 'locale' => 'en-UK', 'value' => 'My Orders', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.order_number', 'locale' => 'pt-PT', 'value' => 'Encomenda #', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.order_number', 'locale' => 'en-UK', 'value' => 'Order #', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.view', 'locale' => 'pt-PT', 'value' => 'Ver', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.view', 'locale' => 'en-UK', 'value' => 'View', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.no_orders', 'locale' => 'pt-PT', 'value' => 'Nenhuma encomenda encontrada.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.no_orders', 'locale' => 'en-UK', 'value' => 'No orders found.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.expected_delivery', 'locale' => 'pt-PT', 'value' => 'Entrega Prevista', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.expected_delivery', 'locale' => 'en-UK', 'value' => 'Expected Delivery', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.track_shipment', 'locale' => 'pt-PT', 'value' => 'Rastrear a Sua Encomenda', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.track_shipment', 'locale' => 'en-UK', 'value' => 'Track Your Shipment', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.no_tracking', 'locale' => 'pt-PT', 'value' => 'A sua encomenda ainda não tem informações de rastreamento', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.no_tracking', 'locale' => 'en-UK', 'value' => 'Your order does not have tracking information yet', 'created_at' => $now, 'updated_at' => $now],

            // Products page
            ['key' => 'products.title', 'locale' => 'pt-PT', 'value' => 'Produtos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.title', 'locale' => 'en-UK', 'value' => 'Products', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.name', 'locale' => 'pt-PT', 'value' => 'Nome', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.name', 'locale' => 'en-UK', 'value' => 'Name', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.category', 'locale' => 'pt-PT', 'value' => 'Categoria', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.category', 'locale' => 'en-UK', 'value' => 'Category', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.material', 'locale' => 'pt-PT', 'value' => 'Material', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.material', 'locale' => 'en-UK', 'value' => 'Material', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.search', 'locale' => 'pt-PT', 'value' => 'Pesquisar...', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.search', 'locale' => 'en-UK', 'value' => 'Search...', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.new', 'locale' => 'pt-PT', 'value' => 'Novo', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.new', 'locale' => 'en-UK', 'value' => 'New', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.only_new', 'locale' => 'pt-PT', 'value' => 'Apenas novos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.only_new', 'locale' => 'en-UK', 'value' => 'Only New', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.not_new', 'locale' => 'pt-PT', 'value' => 'Não novos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.not_new', 'locale' => 'en-UK', 'value' => 'Not New', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.promo', 'locale' => 'pt-PT', 'value' => 'Promoção', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.promo', 'locale' => 'en-UK', 'value' => 'Promo', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.only_promo', 'locale' => 'pt-PT', 'value' => 'Apenas promoções', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.only_promo', 'locale' => 'en-UK', 'value' => 'Only Promo', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.not_promo', 'locale' => 'pt-PT', 'value' => 'Não em promoção', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.not_promo', 'locale' => 'en-UK', 'value' => 'Not Promo', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.in_stock', 'locale' => 'pt-PT', 'value' => 'Em stock', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.in_stock', 'locale' => 'en-UK', 'value' => 'In stock', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.apply', 'locale' => 'pt-PT', 'value' => 'Filtrar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.apply', 'locale' => 'en-UK', 'value' => 'Filter', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.filter.reset', 'locale' => 'pt-PT', 'value' => 'Limpar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.filter.reset', 'locale' => 'en-UK', 'value' => 'Reset', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.no_products', 'locale' => 'pt-PT', 'value' => 'Nenhum produto encontrado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.no_products', 'locale' => 'en-UK', 'value' => 'No products found.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.no_photos', 'locale' => 'pt-PT', 'value' => 'Sem fotos disponíveis', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.no_photos', 'locale' => 'en-UK', 'value' => 'No photos available', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.weight', 'locale' => 'pt-PT', 'value' => 'Peso', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.weight', 'locale' => 'en-UK', 'value' => 'Weight', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.stock', 'locale' => 'pt-PT', 'value' => 'Stock', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.stock', 'locale' => 'en-UK', 'value' => 'Stock', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.available', 'locale' => 'pt-PT', 'value' => 'Disponível', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.available', 'locale' => 'en-UK', 'value' => 'Available', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.out_of_stock', 'locale' => 'pt-PT', 'value' => 'Esgotado', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.out_of_stock', 'locale' => 'en-UK', 'value' => 'Out of stock', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.categories', 'locale' => 'pt-PT', 'value' => 'Categorias', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.categories', 'locale' => 'en-UK', 'value' => 'Categories', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.materials', 'locale' => 'pt-PT', 'value' => 'Materiais', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.materials', 'locale' => 'en-UK', 'value' => 'Materials', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.add_to_cart', 'locale' => 'pt-PT', 'value' => 'Adicionar ao carrinho', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.add_to_cart', 'locale' => 'en-UK', 'value' => 'Add to cart', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.expected_delivery', 'locale' => 'pt-PT', 'value' => 'Entrega prevista', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.expected_delivery', 'locale' => 'en-UK', 'value' => 'Expected delivery', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.delivery_working_days', 'locale' => 'pt-PT', 'value' => 'Calculado em dias úteis (Seg-Sex)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.delivery_working_days', 'locale' => 'en-UK', 'value' => 'Calculated in working days (Mon-Fri)', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.backorder_title', 'locale' => 'pt-PT', 'value' => 'Feito por encomenda', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.backorder_title', 'locale' => 'en-UK', 'value' => 'Made to order', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.backorder_message', 'locale' => 'pt-PT', 'value' => 'Este artigo não tem stock, mas pode ser impresso por encomenda. O tempo de produção é', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.backorder_message', 'locale' => 'en-UK', 'value' => 'This item does not have stock, but can be printed per request. The production time is', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.backorder_delivery_note', 'locale' => 'pt-PT', 'value' => 'A estimativa de data de entrega apresentada já inclui este tempo de produção.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.backorder_delivery_note', 'locale' => 'en-UK', 'value' => 'The shown delivery date estimation already includes this production time.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'products.working_days', 'locale' => 'pt-PT', 'value' => 'dias úteis', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'products.working_days', 'locale' => 'en-UK', 'value' => 'working days', 'created_at' => $now, 'updated_at' => $now],

            // Checkout additional
            ['key' => 'checkout.title', 'locale' => 'pt-PT', 'value' => 'Finalizar Compra', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.title', 'locale' => 'en-UK', 'value' => 'Checkout', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.summary', 'locale' => 'pt-PT', 'value' => 'Resumo', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.summary', 'locale' => 'en-UK', 'value' => 'Summary', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.shipping', 'locale' => 'pt-PT', 'value' => 'Envio', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.shipping', 'locale' => 'en-UK', 'value' => 'Shipping', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.products_tax', 'locale' => 'pt-PT', 'value' => 'Imposto dos produtos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.products_tax', 'locale' => 'en-UK', 'value' => 'Products tax', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.shipping_tax', 'locale' => 'pt-PT', 'value' => 'Imposto de envio', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.shipping_tax', 'locale' => 'en-UK', 'value' => 'Shipping tax', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.total_tax', 'locale' => 'pt-PT', 'value' => 'Total imposto', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.total_tax', 'locale' => 'en-UK', 'value' => 'Total tax', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.total', 'locale' => 'pt-PT', 'value' => 'Total', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.total', 'locale' => 'en-UK', 'value' => 'Total', 'created_at' => $now, 'updated_at' => $now],

            // Profile
            ['key' => 'profile.addresses', 'locale' => 'pt-PT', 'value' => 'Moradas', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.addresses', 'locale' => 'en-UK', 'value' => 'Addresses', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.default_address', 'locale' => 'pt-PT', 'value' => 'Morada predefinida', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.default_address', 'locale' => 'en-UK', 'value' => 'Default address', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.save', 'locale' => 'pt-PT', 'value' => 'Guardar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.save', 'locale' => 'en-UK', 'value' => 'Save', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.delete', 'locale' => 'pt-PT', 'value' => 'Eliminar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.delete', 'locale' => 'en-UK', 'value' => 'Delete', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.no_addresses', 'locale' => 'pt-PT', 'value' => 'Ainda sem moradas.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.no_addresses', 'locale' => 'en-UK', 'value' => 'No addresses yet.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.add_new_address', 'locale' => 'pt-PT', 'value' => 'Adicionar nova morada', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.add_new_address', 'locale' => 'en-UK', 'value' => 'Add new address', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.address_title', 'locale' => 'pt-PT', 'value' => 'Nome da morada', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.address_title', 'locale' => 'en-UK', 'value' => 'Address name', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.address_nif_optional', 'locale' => 'pt-PT', 'value' => 'NIF (opcional)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.address_nif_optional', 'locale' => 'en-UK', 'value' => 'NIF (optional)', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.address_phone_optional', 'locale' => 'pt-PT', 'value' => 'Telefone (opcional)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.address_phone_optional', 'locale' => 'en-UK', 'value' => 'Phone (optional)', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.phone', 'locale' => 'pt-PT', 'value' => 'Telefone', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.phone', 'locale' => 'en-UK', 'value' => 'Phone', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.address_line_1', 'locale' => 'pt-PT', 'value' => 'Morada linha 1', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.address_line_1', 'locale' => 'en-UK', 'value' => 'Address line 1', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.address_line_2', 'locale' => 'pt-PT', 'value' => 'Morada linha 2', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.address_line_2', 'locale' => 'en-UK', 'value' => 'Address line 2', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.address_line_2_optional', 'locale' => 'pt-PT', 'value' => 'Morada linha 2 (opcional)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.address_line_2_optional', 'locale' => 'en-UK', 'value' => 'Address line 2 (optional)', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.address_postal_code', 'locale' => 'pt-PT', 'value' => 'Código postal', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.address_postal_code', 'locale' => 'en-UK', 'value' => 'Postal code', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.address_city', 'locale' => 'pt-PT', 'value' => 'Cidade', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.address_city', 'locale' => 'en-UK', 'value' => 'City', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.address_country', 'locale' => 'pt-PT', 'value' => 'País', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.address_country', 'locale' => 'en-UK', 'value' => 'Country', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.add_address', 'locale' => 'pt-PT', 'value' => 'Adicionar morada', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.add_address', 'locale' => 'en-UK', 'value' => 'Add Address', 'created_at' => $now, 'updated_at' => $now],

            // Profile success messages
            ['key' => 'profile.updated_success', 'locale' => 'pt-PT', 'value' => 'Perfil atualizado com sucesso!', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.updated_success', 'locale' => 'en-UK', 'value' => 'Profile updated successfully!', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.address_added_success', 'locale' => 'pt-PT', 'value' => 'Morada adicionada com sucesso!', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.address_added_success', 'locale' => 'en-UK', 'value' => 'Address added successfully!', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.address_updated_success', 'locale' => 'pt-PT', 'value' => 'Morada atualizada com sucesso!', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.address_updated_success', 'locale' => 'en-UK', 'value' => 'Address updated successfully!', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.address_deleted_success', 'locale' => 'pt-PT', 'value' => 'Morada eliminada com sucesso!', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.address_deleted_success', 'locale' => 'en-UK', 'value' => 'Address deleted successfully!', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.password_updated_success', 'locale' => 'pt-PT', 'value' => 'Palavra-passe atualizada com sucesso!', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.password_updated_success', 'locale' => 'en-UK', 'value' => 'Password updated successfully!', 'created_at' => $now, 'updated_at' => $now],

            // Profile forms
            ['key' => 'profile.profile_information', 'locale' => 'pt-PT', 'value' => 'Informação do Perfil', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.profile_information', 'locale' => 'en-UK', 'value' => 'Profile Information', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.save', 'locale' => 'pt-PT', 'value' => 'Guardar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.save', 'locale' => 'en-UK', 'value' => 'Save', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.saved', 'locale' => 'pt-PT', 'value' => 'Guardado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.saved', 'locale' => 'en-UK', 'value' => 'Saved.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.current_password', 'locale' => 'pt-PT', 'value' => 'Palavra-passe Atual', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.current_password', 'locale' => 'en-UK', 'value' => 'Current Password', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.new_password', 'locale' => 'pt-PT', 'value' => 'Nova Palavra-passe', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.new_password', 'locale' => 'en-UK', 'value' => 'New Password', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.confirm_password', 'locale' => 'pt-PT', 'value' => 'Confirmar Palavra-passe', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.confirm_password', 'locale' => 'en-UK', 'value' => 'Confirm Password', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.update_profile_info_desc', 'locale' => 'pt-PT', 'value' => 'Atualize as informações do perfil e endereço de email da sua conta.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.update_profile_info_desc', 'locale' => 'en-UK', 'value' => "Update your account's profile information and email address.", 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.email_unverified', 'locale' => 'pt-PT', 'value' => 'O seu endereço de email não está verificado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.email_unverified', 'locale' => 'en-UK', 'value' => 'Your email address is unverified.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.resend_verification', 'locale' => 'pt-PT', 'value' => 'Clique aqui para reenviar o email de verificação.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.resend_verification', 'locale' => 'en-UK', 'value' => 'Click here to re-send the verification email.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.verification_sent', 'locale' => 'pt-PT', 'value' => 'Um novo link de verificação foi enviado para o seu endereço de email.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.verification_sent', 'locale' => 'en-UK', 'value' => 'A new verification link has been sent to your email address.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.update_password', 'locale' => 'pt-PT', 'value' => 'Atualizar Palavra-passe', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.update_password', 'locale' => 'en-UK', 'value' => 'Update Password', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.update_password_desc', 'locale' => 'pt-PT', 'value' => 'Certifique-se de que a sua conta está a usar uma palavra-passe longa e aleatória para se manter seguro.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.update_password_desc', 'locale' => 'en-UK', 'value' => 'Ensure your account is using a long, random password to stay secure.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.delete_account', 'locale' => 'pt-PT', 'value' => 'Eliminar Conta', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.delete_account', 'locale' => 'en-UK', 'value' => 'Delete Account', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.delete_account_button', 'locale' => 'pt-PT', 'value' => 'Eliminar Conta', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.delete_account_button', 'locale' => 'en-UK', 'value' => 'Delete Account', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.cancel', 'locale' => 'pt-PT', 'value' => 'Cancelar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.cancel', 'locale' => 'en-UK', 'value' => 'Cancel', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.delete_account_desc', 'locale' => 'pt-PT', 'value' => 'Uma vez que a sua conta é eliminada, todos os seus recursos e dados serão permanentemente eliminados. Antes de eliminar a sua conta, por favor faça o download de quaisquer dados ou informações que deseja reter.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.delete_account_desc', 'locale' => 'en-UK', 'value' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.confirm_delete', 'locale' => 'pt-PT', 'value' => 'Tem a certeza de que deseja eliminar a sua conta?', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.confirm_delete', 'locale' => 'en-UK', 'value' => 'Are you sure you want to delete your account?', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.confirm_delete_desc', 'locale' => 'pt-PT', 'value' => 'Uma vez que a sua conta é eliminada, todos os seus recursos e dados serão permanentemente eliminados. Por favor, insira a sua palavra-passe para confirmar que deseja eliminar permanentemente a sua conta.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.confirm_delete_desc', 'locale' => 'en-UK', 'value' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.', 'created_at' => $now, 'updated_at' => $now],

            // Auth forms
            ['key' => 'auth.remember_me', 'locale' => 'pt-PT', 'value' => 'Lembrar-me', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.remember_me', 'locale' => 'en-UK', 'value' => 'Remember me', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.forgot_password', 'locale' => 'pt-PT', 'value' => 'Esqueceu a sua palavra-passe?', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.forgot_password', 'locale' => 'en-UK', 'value' => 'Forgot your password?', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.login', 'locale' => 'pt-PT', 'value' => 'Entrar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.login', 'locale' => 'en-UK', 'value' => 'Log in', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.already_registered', 'locale' => 'pt-PT', 'value' => 'Já está registado?', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.already_registered', 'locale' => 'en-UK', 'value' => 'Already registered?', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.register', 'locale' => 'pt-PT', 'value' => 'Registar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.register', 'locale' => 'en-UK', 'value' => 'Register', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.email_mismatch', 'locale' => 'pt-PT', 'value' => 'Os emails não coincidem. Verifique os dois campos.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.email_mismatch', 'locale' => 'en-UK', 'value' => 'Email addresses do not match. Please check both fields.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.email', 'locale' => 'pt-PT', 'value' => 'Email', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.email', 'locale' => 'en-UK', 'value' => 'Email', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.password', 'locale' => 'pt-PT', 'value' => 'Palavra-passe', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.password', 'locale' => 'en-UK', 'value' => 'Password', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.confirm_password', 'locale' => 'pt-PT', 'value' => 'Confirmar Palavra-passe', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.confirm_password', 'locale' => 'en-UK', 'value' => 'Confirm Password', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.failed', 'locale' => 'pt-PT', 'value' => 'Credenciais inválidas. Verifique o e-mail e a palavra-passe.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.failed', 'locale' => 'en-UK', 'value' => 'Invalid credentials. Please check your email and password.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.recaptcha_required', 'locale' => 'pt-PT', 'value' => 'Por favor, verifique que não é um robô.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.recaptcha_required', 'locale' => 'en-UK', 'value' => 'Please verify that you are not a robot.', 'created_at' => $now, 'updated_at' => $now],

            // Home page
            ['key' => 'home.banner.tagline', 'locale' => 'pt-PT', 'value' => 'Imprimindo Vida camada por camada', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.banner.tagline', 'locale' => 'en-UK', 'value' => 'Printing Life layer by layer', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.banner.button', 'locale' => 'pt-PT', 'value' => 'NOSSOS SERVIÇOS', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.banner.button', 'locale' => 'en-UK', 'value' => 'OUR SERVICES', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.services.products.title', 'locale' => 'pt-PT', 'value' => 'PRODUTOS', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.services.products.title', 'locale' => 'en-UK', 'value' => 'PRODUCTS', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.services.products.description', 'locale' => 'pt-PT', 'value' => 'Objetos do quotidiano, presentes e lembranças impressas em 3D com precisão e qualidade.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.services.products.description', 'locale' => 'en-UK', 'value' => 'Day to day life objects, gifts, souvenires printed with precision and quality.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.services.products.button', 'locale' => 'pt-PT', 'value' => 'Loja', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.services.products.button', 'locale' => 'en-UK', 'value' => 'Store', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.services.architecture.title', 'locale' => 'pt-PT', 'value' => 'ARQUITETURA', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.services.architecture.title', 'locale' => 'en-UK', 'value' => 'ARCHITECTURE', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.services.architecture.description', 'locale' => 'pt-PT', 'value' => 'Serviço de impressão para arquitetos, engenheiros e criadores. Transforme seus projetos em modelos 3D.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.services.architecture.description', 'locale' => 'en-UK', 'value' => 'Printing service for architects, engineers and creators. Transform your projects into 3D models.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.services.architecture.button', 'locale' => 'pt-PT', 'value' => 'Mais Informações', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.services.architecture.button', 'locale' => 'en-UK', 'value' => 'More Info', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.contact.title', 'locale' => 'pt-PT', 'value' => 'Contacte-nos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.contact.title', 'locale' => 'en-UK', 'value' => 'Contact Us', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.contact.location', 'locale' => 'pt-PT', 'value' => 'Localização', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.contact.location', 'locale' => 'en-UK', 'value' => 'Location', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.contact.phone', 'locale' => 'pt-PT', 'value' => 'Telefone', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.contact.phone', 'locale' => 'en-UK', 'value' => 'Phone', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.contact.email', 'locale' => 'pt-PT', 'value' => 'Email', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.contact.email', 'locale' => 'en-UK', 'value' => 'Email', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.contact.social', 'locale' => 'pt-PT', 'value' => 'Siga-nos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.contact.social', 'locale' => 'en-UK', 'value' => 'Follow Us', 'created_at' => $now, 'updated_at' => $now],

            // Footer
            ['key' => 'footer.about', 'locale' => 'pt-PT', 'value' => 'Serviços de Impressão 3D para todos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer.about', 'locale' => 'en-UK', 'value' => '3D Printing Services for everyone', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'footer.links', 'locale' => 'pt-PT', 'value' => 'Links Rápidos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer.links', 'locale' => 'en-UK', 'value' => 'Quick Links', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'footer.follow', 'locale' => 'pt-PT', 'value' => 'Siga-nos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer.follow', 'locale' => 'en-UK', 'value' => 'Follow Us', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'footer.rights', 'locale' => 'pt-PT', 'value' => 'Todos os direitos reservados.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer.rights', 'locale' => 'en-UK', 'value' => 'All rights reserved.', 'created_at' => $now, 'updated_at' => $now],

            // Architecture page
            ['key' => 'architecture.banner.title', 'locale' => 'pt-PT', 'value' => 'Serviços de Arquitetura', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.banner.title', 'locale' => 'en-UK', 'value' => 'Architecture Services', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.banner.subtitle', 'locale' => 'pt-PT', 'value' => 'Soluções profissionais de impressão 3D para arquitetos e designers', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.banner.subtitle', 'locale' => 'en-UK', 'value' => 'Professional 3D printing solutions for architects and designers', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.banner.button', 'locale' => 'pt-PT', 'value' => 'Solicitar Serviço', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.banner.button', 'locale' => 'en-UK', 'value' => 'Request Service', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.features.title', 'locale' => 'pt-PT', 'value' => 'Nossos Serviços', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.features.title', 'locale' => 'en-UK', 'value' => 'Our Services', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.features.modeling', 'locale' => 'pt-PT', 'value' => 'Modelagem 3D', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.features.modeling', 'locale' => 'en-UK', 'value' => '3D Modeling', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.features.modeling_desc', 'locale' => 'pt-PT', 'value' => 'Preparação profissional de modelos 3D e otimização para impressão.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.features.modeling_desc', 'locale' => 'en-UK', 'value' => 'Professional 3D model preparation and optimization for printing.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.features.materials', 'locale' => 'pt-PT', 'value' => 'Múltiplos Materiais', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.features.materials', 'locale' => 'en-UK', 'value' => 'Multiple Materials', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.features.materials_desc', 'locale' => 'pt-PT', 'value' => 'Escolha entre vários materiais e acabamentos para atender às suas necessidades de projeto.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.features.materials_desc', 'locale' => 'en-UK', 'value' => 'Choose from various materials and finishes to suit your project needs.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.features.support', 'locale' => 'pt-PT', 'value' => 'Suporte de Especialista', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.features.support', 'locale' => 'en-UK', 'value' => 'Expert Support', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.features.support_desc', 'locale' => 'pt-PT', 'value' => 'Suporte dedicado desde a consulta de design até a entrega final.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.features.support_desc', 'locale' => 'en-UK', 'value' => 'Dedicated support from design consultation to final delivery.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.request.title', 'locale' => 'pt-PT', 'value' => 'Solicitar Orçamento', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.request.title', 'locale' => 'en-UK', 'value' => 'Request a Quote', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.request.cta', 'locale' => 'pt-PT', 'value' => 'Quer mais informação ou um orçamento para um projeto específico? Envie-nos um ticket e entraremos em contacto.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.request.cta', 'locale' => 'en-UK', 'value' => 'Want more info or a quote for a specific project? Please send us a ticket and we will follow up.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.request.ticket_button', 'locale' => 'pt-PT', 'value' => 'Criar novo ticket', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.request.ticket_button', 'locale' => 'en-UK', 'value' => 'Create new ticket', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.request.project_details', 'locale' => 'pt-PT', 'value' => 'Detalhes do Projeto', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.request.project_details', 'locale' => 'en-UK', 'value' => 'Project Details', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'architecture.request.submit', 'locale' => 'pt-PT', 'value' => 'Enviar Pedido', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'architecture.request.submit', 'locale' => 'en-UK', 'value' => 'Submit Request', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.new', 'locale' => 'pt-PT', 'value' => 'Novo Ticket', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.new', 'locale' => 'en-UK', 'value' => 'New Ticket', 'created_at' => $now, 'updated_at' => $now],

            // Ticket create form
            ['key' => 'tickets.open_ticket', 'locale' => 'pt-PT', 'value' => 'Abrir Ticket', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.open_ticket', 'locale' => 'en-UK', 'value' => 'Open Ticket', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.category', 'locale' => 'pt-PT', 'value' => 'Categoria', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.category', 'locale' => 'en-UK', 'value' => 'Category', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.title', 'locale' => 'pt-PT', 'value' => 'Título', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.title', 'locale' => 'en-UK', 'value' => 'Title', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.message', 'locale' => 'pt-PT', 'value' => 'Mensagem', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.message', 'locale' => 'en-UK', 'value' => 'Message', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.due_date', 'locale' => 'pt-PT', 'value' => 'Data de Vencimento', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.due_date', 'locale' => 'en-UK', 'value' => 'Due Date', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.files', 'locale' => 'pt-PT', 'value' => 'Ficheiros', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.files', 'locale' => 'en-UK', 'value' => 'Files', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.cancel', 'locale' => 'pt-PT', 'value' => 'Cancelar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.cancel', 'locale' => 'en-UK', 'value' => 'Cancel', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.submit', 'locale' => 'pt-PT', 'value' => 'Abrir Ticket', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.submit', 'locale' => 'en-UK', 'value' => 'Open Ticket', 'created_at' => $now, 'updated_at' => $now],

            // Ticket validation messages
            ['key' => 'tickets.title_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira um título.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.title_required', 'locale' => 'en-UK', 'value' => 'Please enter a title.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.title_max', 'locale' => 'pt-PT', 'value' => 'O título não pode exceder 255 caracteres.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.title_max', 'locale' => 'en-UK', 'value' => 'Title cannot exceed 255 characters.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.category_required', 'locale' => 'pt-PT', 'value' => 'Por favor, selecione uma categoria.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.category_required', 'locale' => 'en-UK', 'value' => 'Please select a category.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.message_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira uma mensagem.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.message_required', 'locale' => 'en-UK', 'value' => 'Please enter a message.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.due_date_invalid', 'locale' => 'pt-PT', 'value' => 'Por favor, insira uma data válida.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.due_date_invalid', 'locale' => 'en-UK', 'value' => 'Please enter a valid date.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.file_max', 'locale' => 'pt-PT', 'value' => 'O ficheiro não pode exceder 20 MB.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.file_max', 'locale' => 'en-UK', 'value' => 'File cannot exceed 20 MB.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.recaptcha_required', 'locale' => 'pt-PT', 'value' => 'Por favor, verifique que não é um robô.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.recaptcha_required', 'locale' => 'en-UK', 'value' => 'Please verify that you are not a robot.', 'created_at' => $now, 'updated_at' => $now],

            // Ticket index page
            ['key' => 'tickets.index_title', 'locale' => 'pt-PT', 'value' => 'Tickets', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.index_title', 'locale' => 'en-UK', 'value' => 'Tickets', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.ticket_id', 'locale' => 'pt-PT', 'value' => 'ID do Ticket', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.ticket_id', 'locale' => 'en-UK', 'value' => 'Ticket ID', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.search_title', 'locale' => 'pt-PT', 'value' => 'Pesquisar título', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.search_title', 'locale' => 'en-UK', 'value' => 'Search title', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.select_category', 'locale' => 'pt-PT', 'value' => 'Selecionar categoria', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.select_category', 'locale' => 'en-UK', 'value' => 'Select category', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.search', 'locale' => 'pt-PT', 'value' => 'Pesquisar...', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.search', 'locale' => 'en-UK', 'value' => 'Search...', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.reset', 'locale' => 'pt-PT', 'value' => 'Limpar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.reset', 'locale' => 'en-UK', 'value' => 'Reset', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.filter', 'locale' => 'pt-PT', 'value' => 'Filtrar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.filter', 'locale' => 'en-UK', 'value' => 'Filter', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.status', 'locale' => 'pt-PT', 'value' => 'Estado', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.status', 'locale' => 'en-UK', 'value' => 'Status', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.last_update', 'locale' => 'pt-PT', 'value' => 'Última Atualização', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.last_update', 'locale' => 'en-UK', 'value' => 'Last Update', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.no_tickets', 'locale' => 'pt-PT', 'value' => 'Nenhum ticket encontrado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.no_tickets', 'locale' => 'en-UK', 'value' => 'No tickets found.', 'created_at' => $now, 'updated_at' => $now],

            // Ticket show page
            ['key' => 'tickets.back_to_tickets', 'locale' => 'pt-PT', 'value' => 'Voltar aos tickets', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.back_to_tickets', 'locale' => 'en-UK', 'value' => 'Back to tickets', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.mark_as_unread', 'locale' => 'pt-PT', 'value' => 'Marcar como não lido', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.mark_as_unread', 'locale' => 'en-UK', 'value' => 'Mark as unread', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.user', 'locale' => 'pt-PT', 'value' => 'Utilizador', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.user', 'locale' => 'en-UK', 'value' => 'User', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.opened', 'locale' => 'pt-PT', 'value' => 'Aberto', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.opened', 'locale' => 'en-UK', 'value' => 'Opened', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.closed', 'locale' => 'pt-PT', 'value' => 'Fechado', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.closed', 'locale' => 'en-UK', 'value' => 'Closed', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.close_reason', 'locale' => 'pt-PT', 'value' => 'Motivo do encerramento', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.close_reason', 'locale' => 'en-UK', 'value' => 'Close reason', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.close_ticket', 'locale' => 'pt-PT', 'value' => 'Fechar Ticket', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.close_ticket', 'locale' => 'en-UK', 'value' => 'Close Ticket', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.reopen_reason', 'locale' => 'pt-PT', 'value' => 'Motivo da reabertura', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.reopen_reason', 'locale' => 'en-UK', 'value' => 'Reopen reason', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.reopen_ticket', 'locale' => 'pt-PT', 'value' => 'Reabrir Ticket', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.reopen_ticket', 'locale' => 'en-UK', 'value' => 'Reopen Ticket', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.system', 'locale' => 'pt-PT', 'value' => 'Sistema', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.system', 'locale' => 'en-UK', 'value' => 'System', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.send', 'locale' => 'pt-PT', 'value' => 'Enviar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.send', 'locale' => 'en-UK', 'value' => 'Send', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.new_message', 'locale' => 'pt-PT', 'value' => 'Nova mensagem', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.new_message', 'locale' => 'en-UK', 'value' => 'New message', 'created_at' => $now, 'updated_at' => $now],

            // Ticket email notifications
            ['key' => 'tickets.email.subject', 'locale' => 'pt-PT', 'value' => 'Ticket #:uuid – :event', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.subject', 'locale' => 'en-UK', 'value' => 'Ticket #:uuid – :event', 'created_at' => $now, 'updated_at' => $now],

            // Orders email translations
            ['key' => 'orders.email.subject', 'locale' => 'pt-PT', 'value' => 'Encomenda #:order_number – :event', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.subject', 'locale' => 'en-UK', 'value' => 'Order #:order_number – :event', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.email.greeting', 'locale' => 'pt-PT', 'value' => 'Olá :name,', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.greeting', 'locale' => 'en-UK', 'value' => 'Hello :name,', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.email.order_label', 'locale' => 'pt-PT', 'value' => 'Encomenda', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.order_label', 'locale' => 'en-UK', 'value' => 'Order', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.email.status_label', 'locale' => 'pt-PT', 'value' => 'Estado', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.status_label', 'locale' => 'en-UK', 'value' => 'Status', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.email.total_label', 'locale' => 'pt-PT', 'value' => 'Total', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.total_label', 'locale' => 'en-UK', 'value' => 'Total', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.email.items_label', 'locale' => 'pt-PT', 'value' => 'Itens', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.items_label', 'locale' => 'en-UK', 'value' => 'Items', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.email.view_button', 'locale' => 'pt-PT', 'value' => 'Ver Encomenda', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.view_button', 'locale' => 'en-UK', 'value' => 'View Order', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.email.auto_sent', 'locale' => 'pt-PT', 'value' => 'Este email foi enviado automaticamente.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.auto_sent', 'locale' => 'en-UK', 'value' => 'This email was sent automatically.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.email.thanks', 'locale' => 'pt-PT', 'value' => 'Obrigado,', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.thanks', 'locale' => 'en-UK', 'value' => 'Thanks,', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.email.event.placed', 'locale' => 'pt-PT', 'value' => 'Encomenda efetuada', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.event.placed', 'locale' => 'en-UK', 'value' => 'Order placed', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.email.event.new', 'locale' => 'pt-PT', 'value' => 'Nova encomenda', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.event.new', 'locale' => 'en-UK', 'value' => 'New order', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'orders.email.event.status_changed', 'locale' => 'pt-PT', 'value' => 'Estado da encomenda atualizado para :status', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.event.status_changed', 'locale' => 'en-UK', 'value' => 'Order status changed to :status', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.greeting', 'locale' => 'pt-PT', 'value' => 'Olá :name,', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.greeting', 'locale' => 'en-UK', 'value' => 'Hello :name,', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.update_intro', 'locale' => 'pt-PT', 'value' => 'Existe uma atualização num ticket em que está envolvido.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.update_intro', 'locale' => 'en-UK', 'value' => 'There is an update on a ticket you are involved in.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.ticket_label', 'locale' => 'pt-PT', 'value' => 'Ticket', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.ticket_label', 'locale' => 'en-UK', 'value' => 'Ticket', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.status_label', 'locale' => 'pt-PT', 'value' => 'Estado', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.status_label', 'locale' => 'en-UK', 'value' => 'Status', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.update_type_label', 'locale' => 'pt-PT', 'value' => 'Tipo de atualização', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.update_type_label', 'locale' => 'en-UK', 'value' => 'Update type', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.message_label', 'locale' => 'pt-PT', 'value' => 'Mensagem', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.message_label', 'locale' => 'en-UK', 'value' => 'Message', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.view_button', 'locale' => 'pt-PT', 'value' => 'Ver Ticket', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.view_button', 'locale' => 'en-UK', 'value' => 'View Ticket', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.auto_sent', 'locale' => 'pt-PT', 'value' => 'Este email foi enviado automaticamente.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.auto_sent', 'locale' => 'en-UK', 'value' => 'This email was sent automatically.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.thanks', 'locale' => 'pt-PT', 'value' => 'Obrigado,', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.thanks', 'locale' => 'en-UK', 'value' => 'Thanks,', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.my_tickets', 'locale' => 'pt-PT', 'value' => 'Tickets', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.my_tickets', 'locale' => 'en-UK', 'value' => 'Tickets', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.orders', 'locale' => 'pt-PT', 'value' => 'Encomendas', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.orders', 'locale' => 'en-UK', 'value' => 'Orders', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.not_a_user', 'locale' => 'pt-PT', 'value' => 'Não é utilizador?', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.not_a_user', 'locale' => 'en-UK', 'value' => 'Not a user?', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.please_register', 'locale' => 'pt-PT', 'value' => 'Por favor registe-se', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.please_register', 'locale' => 'en-UK', 'value' => 'Please register', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.forgot_password_desc', 'locale' => 'pt-PT', 'value' => 'Esqueceu-se da sua palavra-passe? Sem problema. Indique-nos o seu endereço de email e enviaremos um link de redefinição que lhe permitirá escolher uma nova.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.forgot_password_desc', 'locale' => 'en-UK', 'value' => 'Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.email_reset_link', 'locale' => 'pt-PT', 'value' => 'Enviar Link de Redefinição', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.email_reset_link', 'locale' => 'en-UK', 'value' => 'Email Password Reset Link', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.reset_password_button', 'locale' => 'pt-PT', 'value' => 'Redefinir Palavra-passe', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.reset_password_button', 'locale' => 'en-UK', 'value' => 'Reset Password', 'created_at' => $now, 'updated_at' => $now],
            // About page
            ['key' => 'about.banner.title', 'locale' => 'pt-PT', 'value' => 'Sobre a BEKKAS', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.banner.title', 'locale' => 'en-UK', 'value' => 'About BEKKAS', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.banner.subtitle', 'locale' => 'pt-PT', 'value' => 'Tornando a impressão 3D acessível a todos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.banner.subtitle', 'locale' => 'en-UK', 'value' => 'Making 3D printing accessible to everyone', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.mission.title', 'locale' => 'pt-PT', 'value' => 'A Nossa Missão', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.mission.title', 'locale' => 'en-UK', 'value' => 'Our Mission', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.mission.intro', 'locale' => 'pt-PT', 'value' => 'Na BEKKAS, acreditamos que todos merecem acesso ao poder transformador da tecnologia de impressão 3D.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.mission.intro', 'locale' => 'en-UK', 'value' => 'At BEKKAS, we believe that everyone deserves access to the transformative power of 3D printing technology.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.mission.purpose', 'locale' => 'pt-PT', 'value' => 'O nosso negócio está focado em tornar a impressão 3D acessível e económica, dando a todos a oportunidade de ter algo único e personalizável que reflita a sua visão e criatividade.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.mission.purpose', 'locale' => 'en-UK', 'value' => 'Our business is focused on making 3D printing accessible and affordable, giving everybody a chance to have something unique and personalizable that reflects their vision and creativity.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.values.title', 'locale' => 'pt-PT', 'value' => 'Os Nossos Valores', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.values.title', 'locale' => 'en-UK', 'value' => 'What We Stand For', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.values.accessibility', 'locale' => 'pt-PT', 'value' => 'Acessibilidade', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.values.accessibility', 'locale' => 'en-UK', 'value' => 'Accessibility', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.values.accessibility_desc', 'locale' => 'pt-PT', 'value' => 'Tornamos a tecnologia de impressão 3D disponível para todos, desde estudantes a profissionais, com soluções acessíveis e orientação especializada.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.values.accessibility_desc', 'locale' => 'en-UK', 'value' => 'We make 3D printing technology available to everyone, from students to professionals, with affordable solutions and expert guidance.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.values.uniqueness', 'locale' => 'pt-PT', 'value' => 'Originalidade', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.values.uniqueness', 'locale' => 'en-UK', 'value' => 'Uniqueness', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.values.uniqueness_desc', 'locale' => 'pt-PT', 'value' => 'Cada projeto é diferente. Ajudamos a criar algo verdadeiramente único e personalizável que se destaca de produtos produzidos em massa.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.values.uniqueness_desc', 'locale' => 'en-UK', 'value' => 'Every project is different. We help you create something truly unique and personalizable that stands out from mass-produced items.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.values.quality', 'locale' => 'pt-PT', 'value' => 'Qualidade', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.values.quality', 'locale' => 'en-UK', 'value' => 'Quality', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.values.quality_desc', 'locale' => 'pt-PT', 'value' => 'Nunca comprometemos a qualidade. Desde a seleção de materiais até à entrega final, cada etapa é executada com precisão e cuidado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.values.quality_desc', 'locale' => 'en-UK', 'value' => 'We never compromise on quality. From material selection to final delivery, every step is executed with precision and care.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.story.title', 'locale' => 'pt-PT', 'value' => 'A Nossa História', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.story.title', 'locale' => 'en-UK', 'value' => 'Our Story', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.story.paragraph1', 'locale' => 'pt-PT', 'value' => 'A BEKKAS foi fundada com uma visão simples: democratizar o acesso à tecnologia de impressão 3D e capacitar as pessoas a dar vida às suas ideias.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.story.paragraph1', 'locale' => 'en-UK', 'value' => 'BEKKAS was founded with a simple vision: to democratize access to 3D printing technology and empower individuals to bring their ideas to life.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.story.paragraph2', 'locale' => 'pt-PT', 'value' => 'Começámos por servir arquitetos e estudantes, ajudando-os a criar modelos detalhados para os seus projetos. Hoje, servimos uma comunidade diversificada de criadores, oferecendo soluções de impressão 3D personalizadas para qualquer necessidade.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.story.paragraph2', 'locale' => 'en-UK', 'value' => 'We started by serving architects and students, helping them create detailed models for their projects. Today, we serve a diverse community of creators, offering personalized 3D printing solutions for any need.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.story.paragraph3', 'locale' => 'pt-PT', 'value' => 'Seja um protótipo, um presente personalizado, um modelo arquitetónico ou um produto único, estamos aqui para tornar isso realidade. A sua imaginação é o único limite.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.story.paragraph3', 'locale' => 'en-UK', 'value' => 'Whether you need a prototype, a custom gift, an architectural model, or a unique product, we are here to make it happen. Your imagination is the only limit.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.cta.title', 'locale' => 'pt-PT', 'value' => 'Pronto para Começar o Seu Projeto?', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.cta.title', 'locale' => 'en-UK', 'value' => 'Ready to Start Your Project?', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.cta.description', 'locale' => 'pt-PT', 'value' => 'Junte-se a centenas de clientes satisfeitos que deram vida às suas ideias com a BEKKAS.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.cta.description', 'locale' => 'en-UK', 'value' => 'Join hundreds of satisfied customers who have brought their ideas to life with BEKKAS.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.cta.shop', 'locale' => 'pt-PT', 'value' => 'Ver Produtos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.cta.shop', 'locale' => 'en-UK', 'value' => 'Browse Products', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'about.cta.contact', 'locale' => 'pt-PT', 'value' => 'Iniciar Projeto', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about.cta.contact', 'locale' => 'en-UK', 'value' => 'Start a Project', 'created_at' => $now, 'updated_at' => $now],

            // Contact form fields
            ['key' => 'contact.name', 'locale' => 'pt-PT', 'value' => 'Nome', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.name', 'locale' => 'en-UK', 'value' => 'Name', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email', 'locale' => 'pt-PT', 'value' => 'Email', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email', 'locale' => 'en-UK', 'value' => 'Email', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.message', 'locale' => 'pt-PT', 'value' => 'Mensagem', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.message', 'locale' => 'en-UK', 'value' => 'Message', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.send', 'locale' => 'pt-PT', 'value' => 'Enviar Mensagem', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.send', 'locale' => 'en-UK', 'value' => 'Send Message', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.success_message', 'locale' => 'pt-PT', 'value' => 'Obrigado pela sua mensagem! Entraremos em contacto em breve.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.success_message', 'locale' => 'en-UK', 'value' => 'Thank you for your message! We will get back to you soon.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.recaptcha_required', 'locale' => 'pt-PT', 'value' => 'Por favor, verifique que não é um robô.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.recaptcha_required', 'locale' => 'en-UK', 'value' => 'Please verify that you are not a robot.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email.admin_subject', 'locale' => 'pt-PT', 'value' => 'Nova mensagem de contacto de :name', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email.admin_subject', 'locale' => 'en-UK', 'value' => 'New contact message from :name', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email.admin_heading', 'locale' => 'pt-PT', 'value' => 'Nova mensagem de contacto', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email.admin_heading', 'locale' => 'en-UK', 'value' => 'New Contact Message', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email.name_label', 'locale' => 'pt-PT', 'value' => 'Nome', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email.name_label', 'locale' => 'en-UK', 'value' => 'Name', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email.email_label', 'locale' => 'pt-PT', 'value' => 'Email', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email.email_label', 'locale' => 'en-UK', 'value' => 'Email', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email.message_label', 'locale' => 'pt-PT', 'value' => 'Mensagem', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email.message_label', 'locale' => 'en-UK', 'value' => 'Message', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email.thanks', 'locale' => 'pt-PT', 'value' => 'Obrigado,', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email.thanks', 'locale' => 'en-UK', 'value' => 'Thanks,', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email.user_subject', 'locale' => 'pt-PT', 'value' => 'Recebemos a sua mensagem', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email.user_subject', 'locale' => 'en-UK', 'value' => 'We received your message', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email.user_heading', 'locale' => 'pt-PT', 'value' => 'Obrigado, :name', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email.user_heading', 'locale' => 'en-UK', 'value' => 'Thank you, :name', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email.user_body', 'locale' => 'pt-PT', 'value' => 'Recebemos a sua mensagem e entraremos em contacto em breve.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email.user_body', 'locale' => 'en-UK', 'value' => 'We received your message and will get back to you shortly.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email.no_reply', 'locale' => 'pt-PT', 'value' => 'Por favor, não responda a este email.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email.no_reply', 'locale' => 'en-UK', 'value' => 'Please do not reply to this email.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email.user_followup', 'locale' => 'pt-PT', 'value' => 'O seu pedido será respondido em breve.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email.user_followup', 'locale' => 'en-UK', 'value' => 'Your request will be answered soon.', 'created_at' => $now, 'updated_at' => $now],

            // Validation messages
            ['key' => 'validation.name_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira o seu nome.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.name_required', 'locale' => 'en-UK', 'value' => 'Please enter your name.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.name_max', 'locale' => 'pt-PT', 'value' => 'O nome não pode exceder 255 caracteres.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.name_max', 'locale' => 'en-UK', 'value' => 'Name cannot exceed 255 characters.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.email_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira o seu endereço de email.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.email_required', 'locale' => 'en-UK', 'value' => 'Please enter your email address.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.email_invalid', 'locale' => 'pt-PT', 'value' => 'Por favor, insira um endereço de email válido.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.email_invalid', 'locale' => 'en-UK', 'value' => 'Please enter a valid email address.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.email_exists', 'locale' => 'pt-PT', 'value' => 'Este endereço de email já está registado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.email_exists', 'locale' => 'en-UK', 'value' => 'This email address is already registered.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.unique', 'locale' => 'pt-PT', 'value' => 'Este valor já está a ser utilizado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.unique', 'locale' => 'en-UK', 'value' => 'This value is already in use.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.email_mismatch', 'locale' => 'pt-PT', 'value' => 'Os endereços de email não coincidem.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.email_mismatch', 'locale' => 'en-UK', 'value' => 'Email addresses do not match.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.errors_found', 'locale' => 'pt-PT', 'value' => 'Por favor, corrija os seguintes erros:', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.errors_found', 'locale' => 'en-UK', 'value' => 'Please correct the following errors:', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.min.string', 'locale' => 'pt-PT', 'value' => 'Este campo deve ter pelo menos :min caracteres.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.min.string', 'locale' => 'en-UK', 'value' => 'This field must be at least :min characters.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.password_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira uma palavra-passe.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password_required', 'locale' => 'en-UK', 'value' => 'Please enter a password.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.password_min', 'locale' => 'pt-PT', 'value' => 'A palavra-passe deve ter pelo menos 8 caracteres.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password_min', 'locale' => 'en-UK', 'value' => 'Password must be at least 8 characters.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.password_letters', 'locale' => 'pt-PT', 'value' => 'A palavra-passe deve conter pelo menos uma letra.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password_letters', 'locale' => 'en-UK', 'value' => 'Password must contain at least one letter.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.password_mixed_case', 'locale' => 'pt-PT', 'value' => 'A palavra-passe deve conter letras maiúsculas e minúsculas.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password_mixed_case', 'locale' => 'en-UK', 'value' => 'Password must contain both uppercase and lowercase letters.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.password_numbers', 'locale' => 'pt-PT', 'value' => 'A palavra-passe deve conter pelo menos um número.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password_numbers', 'locale' => 'en-UK', 'value' => 'Password must contain at least one number.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.password_symbols', 'locale' => 'pt-PT', 'value' => 'A palavra-passe deve conter pelo menos um símbolo.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password_symbols', 'locale' => 'en-UK', 'value' => 'Password must contain at least one symbol.', 'created_at' => $now, 'updated_at' => $now],

            // Laravel Password rule default message keys
            ['key' => 'validation.password.letters', 'locale' => 'pt-PT', 'value' => 'A palavra-passe deve conter pelo menos uma letra.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password.letters', 'locale' => 'en-UK', 'value' => 'Password must contain at least one letter.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.password.mixed', 'locale' => 'pt-PT', 'value' => 'A palavra-passe deve conter letras maiúsculas e minúsculas.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password.mixed', 'locale' => 'en-UK', 'value' => 'Password must contain both uppercase and lowercase letters.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.password.numbers', 'locale' => 'pt-PT', 'value' => 'A palavra-passe deve conter pelo menos um número.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password.numbers', 'locale' => 'en-UK', 'value' => 'Password must contain at least one number.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.password.symbols', 'locale' => 'pt-PT', 'value' => 'A palavra-passe deve conter pelo menos um símbolo.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password.symbols', 'locale' => 'en-UK', 'value' => 'Password must contain at least one symbol.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.password.uncompromised', 'locale' => 'pt-PT', 'value' => 'Esta palavra-passe apareceu numa fuga de dados. Por favor, escolha uma diferente.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password.uncompromised', 'locale' => 'en-UK', 'value' => 'This password has appeared in a data leak. Please choose a different one.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.password_uncompromised', 'locale' => 'pt-PT', 'value' => 'Esta palavra-passe apareceu numa fuga de dados. Por favor, escolha uma diferente.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password_uncompromised', 'locale' => 'en-UK', 'value' => 'This password has appeared in a data leak. Please choose a different one.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.password_mismatch', 'locale' => 'pt-PT', 'value' => 'As palavras-passe não coincidem.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.password_mismatch', 'locale' => 'en-UK', 'value' => 'Passwords do not match.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.current_password_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira a sua palavra-passe atual.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.current_password_required', 'locale' => 'en-UK', 'value' => 'Please enter your current password.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.current_password_incorrect', 'locale' => 'pt-PT', 'value' => 'A palavra-passe atual está incorreta.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.current_password_incorrect', 'locale' => 'en-UK', 'value' => 'Current password is incorrect.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.recaptcha_required', 'locale' => 'pt-PT', 'value' => 'Por favor, verifique que não é um robô.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.recaptcha_required', 'locale' => 'en-UK', 'value' => 'Please verify that you are not a robot.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.title_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira um título para a morada.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.title_required', 'locale' => 'en-UK', 'value' => 'Please enter an address title.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.nif_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira o seu NIF/Contribuinte.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.nif_required', 'locale' => 'en-UK', 'value' => 'Please enter your NIF/Tax ID.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.address_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira a sua morada.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.address_required', 'locale' => 'en-UK', 'value' => 'Please enter your address.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.address2_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira a linha 2 da morada.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.address2_required', 'locale' => 'en-UK', 'value' => 'Please enter address line 2.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.postal_code_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira o seu código postal.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.postal_code_required', 'locale' => 'en-UK', 'value' => 'Please enter your postal code.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.city_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira a sua cidade.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.city_required', 'locale' => 'en-UK', 'value' => 'Please enter your city.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.country_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira o seu país.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.country_required', 'locale' => 'en-UK', 'value' => 'Please enter your country.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.message_required', 'locale' => 'pt-PT', 'value' => 'Por favor, insira a sua mensagem.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.message_required', 'locale' => 'en-UK', 'value' => 'Please enter your message.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.message_max', 'locale' => 'pt-PT', 'value' => 'A mensagem não pode exceder 5000 caracteres.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.message_max', 'locale' => 'en-UK', 'value' => 'Message cannot exceed 5000 characters.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.error_heading', 'locale' => 'pt-PT', 'value' => 'Por favor, corrija os seguintes erros:', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.error_heading', 'locale' => 'en-UK', 'value' => 'Please fix the following errors:', 'created_at' => $now, 'updated_at' => $now],

            // Stock validation messages
            ['key' => 'stock.out_of_stock', 'locale' => 'pt-PT', 'value' => 'Este produto está esgotado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'stock.out_of_stock', 'locale' => 'en-UK', 'value' => 'This product is out of stock.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'stock.only_available', 'locale' => 'pt-PT', 'value' => 'Apenas :stock unidades disponíveis em stock.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'stock.only_available', 'locale' => 'en-UK', 'value' => 'Only :stock units available in stock.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'stock.insufficient_stock', 'locale' => 'pt-PT', 'value' => ':name tem apenas :stock unidades disponíveis (tem :qty no carrinho).', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'stock.insufficient_stock', 'locale' => 'en-UK', 'value' => ':name has only :stock units available (you have :qty in cart).', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'stock.insufficient_for_order', 'locale' => 'pt-PT', 'value' => ':name tem stock insuficiente. Disponível: :stock, Solicitado: :qty', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'stock.insufficient_for_order', 'locale' => 'en-UK', 'value' => ':name has insufficient stock. Available: :stock, Requested: :qty', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'stock.product_not_found', 'locale' => 'pt-PT', 'value' => 'Produto ID :id não encontrado ou inativo.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'stock.product_not_found', 'locale' => 'en-UK', 'value' => 'Product ID :id not found or inactive.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'stock.is_out_of_stock', 'locale' => 'pt-PT', 'value' => ':name está esgotado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'stock.is_out_of_stock', 'locale' => 'en-UK', 'value' => ':name is out of stock.', 'created_at' => $now, 'updated_at' => $now],

            // Checkout validation messages
            ['key' => 'checkout.validation.address_required', 'locale' => 'pt-PT', 'value' => 'Por favor, selecione um endereço ou forneça um novo.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.address_required', 'locale' => 'en-UK', 'value' => 'Please select an address or provide a new one.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.address_invalid', 'locale' => 'pt-PT', 'value' => 'O endereço selecionado é inválido.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.address_invalid', 'locale' => 'en-UK', 'value' => 'The selected address is invalid.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.title_required', 'locale' => 'pt-PT', 'value' => 'Por favor, forneça um título para o endereço.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.title_required', 'locale' => 'en-UK', 'value' => 'Please provide an address title.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.nif_invalid', 'locale' => 'pt-PT', 'value' => 'O formato do NIF/Número de contribuinte é inválido.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.nif_invalid', 'locale' => 'en-UK', 'value' => 'NIF/VAT number format is invalid.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.nif_optional', 'locale' => 'pt-PT', 'value' => 'NIF (opcional)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.nif_optional', 'locale' => 'en-UK', 'value' => 'NIF (optional)', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.phone_optional', 'locale' => 'pt-PT', 'value' => 'Telefone (opcional)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.phone_optional', 'locale' => 'en-UK', 'value' => 'Phone (optional)', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.address_line_1_required', 'locale' => 'pt-PT', 'value' => 'O endereço é obrigatório.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.address_line_1_required', 'locale' => 'en-UK', 'value' => 'Address is required.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.address_line_2_optional', 'locale' => 'pt-PT', 'value' => 'Morada linha 2 (opcional)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.address_line_2_optional', 'locale' => 'en-UK', 'value' => 'Address line 2 (optional)', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.postal_code_required', 'locale' => 'pt-PT', 'value' => 'O código postal é obrigatório.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.postal_code_required', 'locale' => 'en-UK', 'value' => 'Postal code is required.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.city_required', 'locale' => 'pt-PT', 'value' => 'A cidade é obrigatória.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.city_required', 'locale' => 'en-UK', 'value' => 'City is required.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.country_required', 'locale' => 'pt-PT', 'value' => 'O país é obrigatório.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.country_required', 'locale' => 'en-UK', 'value' => 'Country is required.', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('static_translations')->upsert($rows, ['key', 'locale']);
    }
}
