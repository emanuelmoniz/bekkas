<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaticTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Context descriptions: keyed by prefix (longer/more-specific prefixes first).
        // Used to set the context column on INSERT only — admin edits are never overwritten.
        $contextMap = [
            'checkout.pay.'              => 'Checkout — payment widget page (Easypay SDK)',
            'checkout.validation.'       => 'Checkout — address form validation messages',
            'checkout.'                  => 'Checkout page — address & shipping selection',
            'orders.email.'              => 'Order email notifications',
            'orders.'                    => 'My Orders page & order detail page',
            'store.badge.'               => 'Store — product card badges',
            'store.filter.'              => 'Store — filter panel',
            'store.order.'               => 'Store — sort options',
            'store.'                     => 'Store — product listing & detail page',
            'favorites.filter.'          => 'My Favorites page — filter panel',
            'favorites.'                 => 'My Favorites page',
            'nav.'                       => 'Global navigation menu (all pages)',
            'page.'                      => 'Page title tags',
            'cart.'                      => 'Shopping cart page',
            'shipping_config.'           => 'Admin — shipping configuration page',
            'profile.'                   => 'User profile page — settings, addresses, password',
            'auth.'                      => 'Auth pages — login, register, password reset, email verification',
            'home.banner.'               => 'Home page — hero banner',
            'home.services.'             => 'Home page — services section',
            'home.contact.'              => 'Home page — contact preview section',
            'home.'                      => 'Home page',
            'footer.'                    => 'Global footer (all pages)',
            'custom.banner.'             => 'Custom services page — hero banner',
            'custom.features.'           => 'Custom services page — features section',
            'custom.request.'            => 'Custom services page — quote request section',
            'custom.'                    => 'Custom services page',
            'tickets.email.'             => 'Ticket email notifications',
            'tickets.'                   => 'Support tickets — create, list, detail pages',
            'about.banner.'              => 'About page — hero banner',
            'about.mission.'             => 'About page — mission section',
            'about.values.'              => 'About page — values section',
            'about.story.'               => 'About page — story section',
            'about.cta.'                 => 'About page — call to action section',
            'about.'                     => 'About page',
            'legal.terms.'               => 'Terms of Service page',
            'legal.privacy.'             => 'Privacy Policy page',
            'legal.'                     => 'Legal pages',
            'contact.email.'             => 'Contact form email notifications',
            'contact.'                   => 'Contact page & contact form',
            'validation.password.'       => 'Shared validation — password rules',
            'validation.'                => 'Shared form validation messages',
            'stock.'                     => 'Cart & checkout — stock validation messages',
            'error.'                     => 'Error pages (404, 500)',
            'pagination.'                => 'Shared pagination component (all listing pages)',
            'gallery.'                   => 'Image gallery component (product & project pages)',
            'tax.'                       => 'Cart & checkout — tax display',
            'verification-link-sent'     => 'Profile page — email verification status',
        ];

        // Sort by prefix length descending so the most-specific prefix wins.
        uksort($contextMap, fn($a, $b) => strlen($b) <=> strlen($a));

        $getContext = function (string $key) use ($contextMap): string {
            foreach ($contextMap as $prefix => $context) {
                if (str_starts_with($key, $prefix)) {
                    return $context;
                }
            }
            return '';
        };

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
            // Pagination summary (used by component)
            ['key' => 'pagination.showing', 'locale' => 'pt-PT', 'value' => 'A mostrar :first a :last de :total resultados', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'pagination.showing', 'locale' => 'en-UK', 'value' => 'Showing :first to :last of :total results', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'pagination.previous', 'locale' => 'pt-PT', 'value' => 'Anterior', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'pagination.previous', 'locale' => 'en-UK', 'value' => 'Previous', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'pagination.next', 'locale' => 'pt-PT', 'value' => 'Seguinte', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'pagination.next', 'locale' => 'en-UK', 'value' => 'Next', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'pagination.navigation', 'locale' => 'pt-PT', 'value' => 'Navegação de paginação', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'pagination.navigation', 'locale' => 'en-UK', 'value' => 'Pagination Navigation', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'pagination.goto_page', 'locale' => 'pt-PT', 'value' => 'Ir para a página :page', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'pagination.goto_page', 'locale' => 'en-UK', 'value' => 'Go to page :page', 'created_at' => $now, 'updated_at' => $now],
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

            ['key' => 'nav.store', 'locale' => 'pt-PT', 'value' => 'Loja', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.store', 'locale' => 'en-UK', 'value' => 'Store', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'nav.custom', 'locale' => 'pt-PT', 'value' => 'Produção', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.custom', 'locale' => 'en-UK', 'value' => 'Custom', 'created_at' => $now, 'updated_at' => $now],

            // store submenu entries
            ['key' => 'nav.store.all_products', 'locale' => 'pt-PT', 'value' => 'Todos os Produtos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.store.all_products', 'locale' => 'en-UK', 'value' => 'All Products', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.store.featured', 'locale' => 'pt-PT', 'value' => 'Em Destaque', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.store.featured', 'locale' => 'en-UK', 'value' => 'Featured', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.store.promotion', 'locale' => 'pt-PT', 'value' => 'Promoção', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nav.store.promotion', 'locale' => 'en-UK', 'value' => 'Promotion', 'created_at' => $now, 'updated_at' => $now],

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

            ['key' => 'store.title', 'locale' => 'pt-PT', 'value' => 'Loja', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.title', 'locale' => 'en-UK', 'value' => 'Store', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.filter.name', 'locale' => 'pt-PT', 'value' => 'Nome', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.filter.name', 'locale' => 'en-UK', 'value' => 'Name', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.filter.filters', 'locale' => 'pt-PT', 'value' => 'Filtros', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.filter.filters', 'locale' => 'en-UK', 'value' => 'Filters', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.filter.in_stock', 'locale' => 'pt-PT', 'value' => 'Em stock', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.filter.in_stock', 'locale' => 'en-UK', 'value' => 'In Stock', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.filter.in_promotion', 'locale' => 'pt-PT', 'value' => 'Em promoção', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.filter.in_promotion', 'locale' => 'en-UK', 'value' => 'In Promotion', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.filter.featured', 'locale' => 'pt-PT', 'value' => 'Destaque', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.filter.featured', 'locale' => 'en-UK', 'value' => 'Featured', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.filter.categories', 'locale' => 'pt-PT', 'value' => 'Categorias', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.filter.categories', 'locale' => 'en-UK', 'value' => 'Categories', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.filter.materials', 'locale' => 'pt-PT', 'value' => 'Materiais', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.related_products', 'locale' => 'pt-PT', 'value' => 'Produtos Relacionados', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.related_products', 'locale' => 'en-UK', 'value' => 'Related Products', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.scroll_prev', 'locale' => 'pt-PT', 'value' => 'Anterior', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.scroll_prev', 'locale' => 'en-UK', 'value' => 'Previous', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.scroll_next', 'locale' => 'pt-PT', 'value' => 'Seguinte', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.scroll_next', 'locale' => 'en-UK', 'value' => 'Next', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.filter.materials', 'locale' => 'en-UK', 'value' => 'Materials', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.filter.apply', 'locale' => 'pt-PT', 'value' => 'Filtrar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.filter.apply', 'locale' => 'en-UK', 'value' => 'Filter', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.filter.reset', 'locale' => 'pt-PT', 'value' => 'Limpar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.filter.reset', 'locale' => 'en-UK', 'value' => 'Reset', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.filter.price_range', 'locale' => 'pt-PT', 'value' => 'Intervalo de Preço', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.filter.price_range', 'locale' => 'en-UK', 'value' => 'Price Range', 'created_at' => $now, 'updated_at' => $now],

            // badges shown on product cards
            ['key' => 'store.badge.featured', 'locale' => 'pt-PT', 'value' => 'DESTAQUE', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.badge.featured', 'locale' => 'en-UK', 'value' => 'FEATURED', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.badge.promo', 'locale' => 'pt-PT', 'value' => 'SALDOS', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.badge.promo', 'locale' => 'en-UK', 'value' => 'PROMO', 'created_at' => $now, 'updated_at' => $now],

            // ordering options
            ['key' => 'store.order.default', 'locale' => 'pt-PT', 'value' => 'Ordenar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.order.default', 'locale' => 'en-UK', 'value' => 'Sort', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.order.name_az', 'locale' => 'pt-PT', 'value' => 'Nome A‑Z', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.order.name_az', 'locale' => 'en-UK', 'value' => 'Name A-Z', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.order.name_za', 'locale' => 'pt-PT', 'value' => 'Nome Z‑A', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.order.name_za', 'locale' => 'en-UK', 'value' => 'Name Z-A', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.order.price_low_high', 'locale' => 'pt-PT', 'value' => 'Preço Crescente', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.order.price_low_high', 'locale' => 'en-UK', 'value' => 'Price Low-High', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.order.price_high_low', 'locale' => 'pt-PT', 'value' => 'Preço Decrescente', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.order.price_high_low', 'locale' => 'en-UK', 'value' => 'Price High-Low', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.order.featured_first', 'locale' => 'pt-PT', 'value' => 'Destaques Primeiro', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.order.featured_first', 'locale' => 'en-UK', 'value' => 'Featured First', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.order.promo_first', 'locale' => 'pt-PT', 'value' => 'Promoções Primeiro', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.order.promo_first', 'locale' => 'en-UK', 'value' => 'Promo First', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.no_products', 'locale' => 'pt-PT', 'value' => 'Nenhum produto encontrado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.no_products', 'locale' => 'en-UK', 'value' => 'No products found.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.no_photos', 'locale' => 'pt-PT', 'value' => 'Sem fotos disponíveis', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.no_photos', 'locale' => 'en-UK', 'value' => 'No photos available', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.description', 'locale' => 'pt-PT', 'value' => 'Descrição', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.description', 'locale' => 'en-UK', 'value' => 'Description', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.technical_info', 'locale' => 'pt-PT', 'value' => 'Informação técnica', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.technical_info', 'locale' => 'en-UK', 'value' => 'Technical info', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.weight', 'locale' => 'pt-PT', 'value' => 'Peso', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.weight', 'locale' => 'en-UK', 'value' => 'Weight', 'created_at' => $now, 'updated_at' => $now],

            // dimensions for products (shown in mm)
            ['key' => 'store.dimensions', 'locale' => 'pt-PT', 'value' => 'Dimensões', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.dimensions', 'locale' => 'en-UK', 'value' => 'Dimensions', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.stock', 'locale' => 'pt-PT', 'value' => 'Stock', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.stock', 'locale' => 'en-UK', 'value' => 'Stock', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.available', 'locale' => 'pt-PT', 'value' => 'Disponível', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.available', 'locale' => 'en-UK', 'value' => 'Available', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.out_of_stock', 'locale' => 'pt-PT', 'value' => 'Esgotado', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.out_of_stock', 'locale' => 'en-UK', 'value' => 'Out of stock', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.categories', 'locale' => 'pt-PT', 'value' => 'Categorias', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.categories', 'locale' => 'en-UK', 'value' => 'Categories', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.materials', 'locale' => 'pt-PT', 'value' => 'Materiais', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.materials', 'locale' => 'en-UK', 'value' => 'Materials', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.add_to_cart', 'locale' => 'pt-PT', 'value' => 'Adicionar ao carrinho', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.add_to_cart', 'locale' => 'en-UK', 'value' => 'Add to cart', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.adding', 'locale' => 'pt-PT', 'value' => 'A adicionar...', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.adding', 'locale' => 'en-UK', 'value' => 'Adding...', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.added_to_cart', 'locale' => 'pt-PT', 'value' => 'Item adicionado ao carrinho', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.added_to_cart', 'locale' => 'en-UK', 'value' => 'Added to cart', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.add_to_cart_failed', 'locale' => 'pt-PT', 'value' => 'Não foi possível adicionar ao carrinho', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.add_to_cart_failed', 'locale' => 'en-UK', 'value' => 'Unable to add to cart', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.product_unavailable', 'locale' => 'pt-PT', 'value' => 'Produto indisponível', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.product_unavailable', 'locale' => 'en-UK', 'value' => 'Product unavailable', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.select_option', 'locale' => 'pt-PT', 'value' => 'Selecione uma opção', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.select_option', 'locale' => 'en-UK', 'value' => 'Select an option', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.options_required', 'locale' => 'pt-PT', 'value' => 'Por favor selecione todas as opções antes de adicionar ao carrinho.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.options_required', 'locale' => 'en-UK', 'value' => 'Please select all options before adding to cart.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.select_option_for', 'locale' => 'pt-PT', 'value' => 'Por favor selecione um valor para: :type', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.select_option_for', 'locale' => 'en-UK', 'value' => 'Please select a value for: :type', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.invalid_option_for', 'locale' => 'pt-PT', 'value' => 'Seleção inválida para: :type', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.invalid_option_for', 'locale' => 'en-UK', 'value' => 'Invalid selection for: :type', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.back_to_store', 'locale' => 'pt-PT', 'value' => 'Voltar à loja', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.back_to_store', 'locale' => 'en-UK', 'value' => 'Back to store', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.expected_delivery', 'locale' => 'pt-PT', 'value' => 'Entrega prevista', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.expected_delivery', 'locale' => 'en-UK', 'value' => 'Expected delivery', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.delivery_working_days', 'locale' => 'pt-PT', 'value' => 'Calculado em dias úteis (Seg-Sex)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.delivery_working_days', 'locale' => 'en-UK', 'value' => 'Calculated in working days (Mon-Fri)', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.backorder_title', 'locale' => 'pt-PT', 'value' => 'Feito por encomenda', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.backorder_title', 'locale' => 'en-UK', 'value' => 'Made to order', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.backorder_message', 'locale' => 'pt-PT', 'value' => 'Este artigo não tem stock, mas pode ser impresso por encomenda. O tempo de produção é', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.backorder_message', 'locale' => 'en-UK', 'value' => 'This item does not have stock, but can be printed per request. The production time is', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.backorder_delivery_note', 'locale' => 'pt-PT', 'value' => 'A estimativa de data de entrega apresentada já inclui este tempo de produção.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.backorder_delivery_note', 'locale' => 'en-UK', 'value' => 'The shown delivery date estimation already includes this production time.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.working_days', 'locale' => 'pt-PT', 'value' => 'dias úteis', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.working_days', 'locale' => 'en-UK', 'value' => 'working days', 'created_at' => $now, 'updated_at' => $now],

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

            ['key' => 'profile.social_accounts', 'locale' => 'pt-PT', 'value' => 'Contas Sociais', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.social_accounts', 'locale' => 'en-UK', 'value' => 'Social accounts', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.social_accounts_desc', 'locale' => 'pt-PT', 'value' => 'Ligue contas externas (por exemplo Google) para iniciar sessão rapidamente.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.social_accounts_desc', 'locale' => 'en-UK', 'value' => 'Link external accounts (Google) to sign in quickly.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.social_google_desc', 'locale' => 'pt-PT', 'value' => 'Use a sua conta Google para iniciar sessão.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.social_google_desc', 'locale' => 'en-UK', 'value' => 'Use your Google account to sign in.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.social_microsoft_desc', 'locale' => 'pt-PT', 'value' => 'Use a sua conta Microsoft para iniciar sessão.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.social_microsoft_desc', 'locale' => 'en-UK', 'value' => 'Use your Microsoft account to sign in.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.link_account', 'locale' => 'pt-PT', 'value' => 'Ligar Conta', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.link_account', 'locale' => 'en-UK', 'value' => 'Link account', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.unlink_account', 'locale' => 'pt-PT', 'value' => 'Desligar Conta', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.unlink_account', 'locale' => 'en-UK', 'value' => 'Unlink', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.social_linked', 'locale' => 'pt-PT', 'value' => 'Conta social ligada.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.social_linked', 'locale' => 'en-UK', 'value' => 'Social account linked.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.social_unlinked', 'locale' => 'pt-PT', 'value' => 'Conta social desligada.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.social_unlinked', 'locale' => 'en-UK', 'value' => 'Social account unlinked.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.confirm_unlink', 'locale' => 'pt-PT', 'value' => 'Tem a certeza de que pretende desligar esta conta social?', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.confirm_unlink', 'locale' => 'en-UK', 'value' => 'Are you sure you want to unlink this social account?', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.cannot_unlink_last_auth', 'locale' => 'pt-PT', 'value' => 'Não é possível desligar o último método de acesso. Adicione uma palavra-passe ou outra conta ligada primeiro.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.cannot_unlink_last_auth', 'locale' => 'en-UK', 'value' => 'Cannot unlink the last sign-in method. Add a password or another linked account first.', 'created_at' => $now, 'updated_at' => $now],

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

            // Backwards-compatible key used by session status in controllers
            ['key' => 'verification-link-sent', 'locale' => 'pt-PT', 'value' => 'Um novo link de verificação foi enviado para o seu endereço de email.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'verification-link-sent', 'locale' => 'en-UK', 'value' => 'A new verification link has been sent to your email address.', 'created_at' => $now, 'updated_at' => $now],

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

            // Deletion-by-email (social-only users)
            ['key' => 'profile.delete_by_email_desc', 'locale' => 'pt-PT', 'value' => 'Enviaremos um link seguro para o seu email para confirmar a eliminação da conta.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.delete_by_email_desc', 'locale' => 'en-UK', 'value' => "We'll email you a secure link to confirm account deletion.", 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.delete_by_email_button', 'locale' => 'pt-PT', 'value' => 'Enviar link de eliminação', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.delete_by_email_button', 'locale' => 'en-UK', 'value' => 'Send deletion link', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.delete_by_email_sent', 'locale' => 'pt-PT', 'value' => 'Um link de eliminação foi enviado para o seu endereço de email.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.delete_by_email_sent', 'locale' => 'en-UK', 'value' => 'A deletion link has been sent to your email address.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.delete_by_email_subject', 'locale' => 'pt-PT', 'value' => 'Confirmar eliminação da conta', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.delete_by_email_subject', 'locale' => 'en-UK', 'value' => 'Confirm account deletion', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.delete_by_email_intro', 'locale' => 'pt-PT', 'value' => 'Clique no botão abaixo para confirmar a eliminação da sua conta.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.delete_by_email_intro', 'locale' => 'en-UK', 'value' => 'Click the button below to confirm deletion of your account.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.delete_by_email_action', 'locale' => 'pt-PT', 'value' => 'Eliminar a minha conta', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.delete_by_email_action', 'locale' => 'en-UK', 'value' => 'Delete my account', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.delete_by_email_outro', 'locale' => 'pt-PT', 'value' => 'Se não pediu isto, ignore este email.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.delete_by_email_outro', 'locale' => 'en-UK', 'value' => 'If you did not request this, ignore this email.', 'created_at' => $now, 'updated_at' => $now],

            // Friendly error shown when deletion fails unexpectedly
            ['key' => 'profile.delete_failed', 'locale' => 'pt-PT', 'value' => 'Falha ao eliminar a conta — por favor contacte o suporte.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.delete_failed', 'locale' => 'en-UK', 'value' => 'Account deletion failed — please contact support.', 'created_at' => $now, 'updated_at' => $now],

            // Auth forms
            ['key' => 'auth.remember_me', 'locale' => 'pt-PT', 'value' => 'Lembrar-me', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.remember_me', 'locale' => 'en-UK', 'value' => 'Remember me', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.forgot_password', 'locale' => 'pt-PT', 'value' => 'Esqueceu a sua palavra-passe?', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.forgot_password', 'locale' => 'en-UK', 'value' => 'Forgot your password?', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.login', 'locale' => 'pt-PT', 'value' => 'Entrar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.login', 'locale' => 'en-UK', 'value' => 'Log in', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.continue_with_google', 'locale' => 'pt-PT', 'value' => 'Continuar com Google', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.continue_with_google', 'locale' => 'en-UK', 'value' => 'Continue with Google', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.continue_with_microsoft', 'locale' => 'pt-PT', 'value' => 'Continuar com Microsoft', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.continue_with_microsoft', 'locale' => 'en-UK', 'value' => 'Continue with Microsoft', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.social_failed', 'locale' => 'pt-PT', 'value' => 'Falha no login social.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.social_failed', 'locale' => 'en-UK', 'value' => 'Social login failed.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.provider_already_linked', 'locale' => 'pt-PT', 'value' => 'Esta conta social já está ligada a outro utilizador.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.provider_already_linked', 'locale' => 'en-UK', 'value' => 'This social account is already linked to another user.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.already_registered', 'locale' => 'pt-PT', 'value' => 'Já está registado?', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.already_registered', 'locale' => 'en-UK', 'value' => 'Already registered?', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.register', 'locale' => 'pt-PT', 'value' => 'Registar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.register', 'locale' => 'en-UK', 'value' => 'Register', 'created_at' => $now, 'updated_at' => $now],

            // Register: terms / privacy acceptance labels (shown on registration form)
            ['key' => 'auth.accept_terms_label', 'locale' => 'pt-PT', 'value' => 'Aceito os <a href=":terms_url" target="_blank" rel="noopener">Termos de Serviço</a>.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.accept_terms_label', 'locale' => 'en-UK', 'value' => 'I accept the <a href=":terms_url" target="_blank" rel="noopener">Terms of Service</a>.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.accept_privacy_label', 'locale' => 'pt-PT', 'value' => 'Aceito a <a href=":privacy_url" target="_blank" rel="noopener">Política de Privacidade</a>.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.accept_privacy_label', 'locale' => 'en-UK', 'value' => 'I accept the <a href=":privacy_url" target="_blank" rel="noopener">Privacy Policy</a>.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.email_mismatch', 'locale' => 'pt-PT', 'value' => 'Os emails não coincidem. Verifique os dois campos.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.email_mismatch', 'locale' => 'en-UK', 'value' => 'Email addresses do not match. Please check both fields.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.email', 'locale' => 'pt-PT', 'value' => 'Email', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.email', 'locale' => 'en-UK', 'value' => 'Email', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.name', 'locale' => 'pt-PT', 'value' => 'Nome', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.name', 'locale' => 'en-UK', 'value' => 'Name', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.confirm_email', 'locale' => 'pt-PT', 'value' => 'Confirmar Email', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.confirm_email', 'locale' => 'en-UK', 'value' => 'Confirm Email', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.password', 'locale' => 'pt-PT', 'value' => 'Palavra-passe', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.password', 'locale' => 'en-UK', 'value' => 'Password', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.confirm_password', 'locale' => 'pt-PT', 'value' => 'Confirmar Palavra-passe', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.confirm_password', 'locale' => 'en-UK', 'value' => 'Confirm Password', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.failed', 'locale' => 'pt-PT', 'value' => 'Credenciais inválidas. Verifique o e-mail e a palavra-passe.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.failed', 'locale' => 'en-UK', 'value' => 'Invalid credentials. Please check your email and password.', 'created_at' => $now, 'updated_at' => $now],

            // Email verification / activation (guest flows)
            ['key' => 'auth.email_unverified', 'locale' => 'pt-PT', 'value' => 'Esta conta ainda não foi confirmada. Verifique o seu email para o link de ativação.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.email_unverified', 'locale' => 'en-UK', 'value' => 'This account has not been confirmed. Please check your email for the activation link.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.email_unverified_notice', 'locale' => 'pt-PT', 'value' => 'Existe uma conta registada com este email, mas ainda não foi confirmada.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.email_unverified_notice', 'locale' => 'en-UK', 'value' => 'An account exists for this email but it has not been confirmed.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.resend_activation', 'locale' => 'pt-PT', 'value' => 'Reenviar email de ativação', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.resend_activation', 'locale' => 'en-UK', 'value' => 'Resend activation email', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.activation_sent', 'locale' => 'pt-PT', 'value' => 'Um novo email de ativação foi enviado para o seu endereço de email.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.activation_sent', 'locale' => 'en-UK', 'value' => 'A new activation email has been sent to your email address.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.check_spam', 'locale' => 'pt-PT', 'value' => 'Se não encontrar a mensagem, verifique a sua pasta de spam.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.check_spam', 'locale' => 'en-UK', 'value' => 'If you do not see the message, please check your spam folder.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.already_verified', 'locale' => 'pt-PT', 'value' => 'O email já foi confirmado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.already_verified', 'locale' => 'en-UK', 'value' => 'Email already verified.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.verify_sent_message', 'locale' => 'pt-PT', 'value' => 'Obrigado pelo registo! Verifique o seu email para confirmar a sua conta.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.verify_sent_message', 'locale' => 'en-UK', 'value' => 'Thanks for signing up! Please check your email for a verification link to confirm your account.', 'created_at' => $now, 'updated_at' => $now],

            // Verification email (localized DB-driven strings)
            ['key' => 'auth.verify_email_subject', 'locale' => 'pt-PT', 'value' => 'Verificar Endereço de Email', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.verify_email_subject', 'locale' => 'en-UK', 'value' => 'Verify Email Address', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.verify_email_intro', 'locale' => 'pt-PT', 'value' => 'Clique no botão abaixo para verificar o seu endereço de email.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.verify_email_intro', 'locale' => 'en-UK', 'value' => 'Please click the button below to verify your email address.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.verify_email_action', 'locale' => 'pt-PT', 'value' => 'Verificar Endereço de Email', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.verify_email_action', 'locale' => 'en-UK', 'value' => 'Verify Email Address', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.verify_email_outro', 'locale' => 'pt-PT', 'value' => 'Se não criou uma conta, nenhuma ação adicional é necessária.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.verify_email_outro', 'locale' => 'en-UK', 'value' => 'If you did not create an account, no further action is required.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.verify_email_subcopy', 'locale' => 'pt-PT', 'value' => 'Se tiver dificuldades em clicar no botão ":actionText", copie e cole o URL abaixo no seu navegador:', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.verify_email_subcopy', 'locale' => 'en-UK', 'value' => "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below into your web browser:", 'created_at' => $now, 'updated_at' => $now],

            // UI for verify-email page
            ['key' => 'auth.verify_prompt', 'locale' => 'pt-PT', 'value' => 'Obrigado pelo registo! Antes de começar, pode verificar o seu endereço de email clicando no link que enviámos? Se não recebeu o email, reenviaremos outro.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.verify_prompt', 'locale' => 'en-UK', 'value' => 'Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.resend_verification_button', 'locale' => 'pt-PT', 'value' => 'Reenviar Email de Verificação', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.resend_verification_button', 'locale' => 'en-UK', 'value' => 'Resend Verification Email', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.verification_verified', 'locale' => 'pt-PT', 'value' => 'O seu email foi confirmado com sucesso.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.verification_verified', 'locale' => 'en-UK', 'value' => 'Your email address has been verified.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'auth.recaptcha_required', 'locale' => 'pt-PT', 'value' => 'Por favor, verifique que não é um robô.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'auth.recaptcha_required', 'locale' => 'en-UK', 'value' => 'Please verify that you are not a robot.', 'created_at' => $now, 'updated_at' => $now],

            // Home page
            ['key' => 'home.banner.tagline1', 'locale' => 'pt-PT', 'value' => 'Criar o futuro, da ideia ao objeto', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.banner.tagline1', 'locale' => 'en-UK', 'value' => 'Printing Life layer by layer', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.banner.button1', 'locale' => 'pt-PT', 'value' => 'SERVIÇOS', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.banner.button1', 'locale' => 'en-UK', 'value' => 'OUR SERVICES', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.banner.tagline2', 'locale' => 'pt-PT', 'value' => 'Brincos inspirados em origami.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.banner.tagline2', 'locale' => 'en-UK', 'value' => 'Origami-inspired earrings.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.banner.button2', 'locale' => 'pt-PT', 'value' => 'LOJA', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.banner.button2', 'locale' => 'en-UK', 'value' => 'STORE', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.banner.tagline3', 'locale' => 'pt-PT', 'value' => 'Imprimimos as tuas ideias e projetos.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.banner.tagline3', 'locale' => 'en-UK', 'value' => 'We print your ideas and projects.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.banner.button3', 'locale' => 'pt-PT', 'value' => 'PRODUÇÃO', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.banner.button3', 'locale' => 'en-UK', 'value' => 'CUSTOM', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.services.store.title', 'locale' => 'pt-PT', 'value' => 'Loja', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.services.store.title', 'locale' => 'en-UK', 'value' => 'Store', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.services.store.description', 'locale' => 'pt-PT', 'value' => 'Objetos do quotidiano, presentes e lembranças impressas em 3D com precisão e qualidade.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.services.store.description', 'locale' => 'en-UK', 'value' => 'Day to day life objects, gifts, souvenires printed with precision and quality.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.services.store.button', 'locale' => 'pt-PT', 'value' => 'Ver Produtos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.services.store.button', 'locale' => 'en-UK', 'value' => 'View Products', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.services.custom.title', 'locale' => 'pt-PT', 'value' => 'Produção', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.services.custom.title', 'locale' => 'en-UK', 'value' => 'Custom', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.services.custom.description', 'locale' => 'pt-PT', 'value' => 'Serviço de impressão para arquitetos, engenheiros e criadores. Transforme seus projetos em modelos 3D.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.services.custom.description', 'locale' => 'en-UK', 'value' => 'Printing service for architects, engineers and creators. Transform your projects into 3D models.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.services.custom.button', 'locale' => 'pt-PT', 'value' => 'Mais Informações', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.services.custom.button', 'locale' => 'en-UK', 'value' => 'More Info', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.contact.title', 'locale' => 'pt-PT', 'value' => 'Contactos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.contact.title', 'locale' => 'en-UK', 'value' => 'Contacts', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.contact.location', 'locale' => 'pt-PT', 'value' => 'Localização', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.contact.location', 'locale' => 'en-UK', 'value' => 'Location', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.contact.phone', 'locale' => 'pt-PT', 'value' => 'Telefone', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.contact.phone', 'locale' => 'en-UK', 'value' => 'Phone', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'home.contact.email', 'locale' => 'pt-PT', 'value' => 'E-mail', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'home.contact.email', 'locale' => 'en-UK', 'value' => 'E-mail', 'created_at' => $now, 'updated_at' => $now],

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
            ['key' => 'footer.designed_by', 'locale' => 'pt-PT', 'value' => 'Criado por :az e :sofia', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer.designed_by', 'locale' => 'en-UK', 'value' => 'Created by :az and :sofia', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'footer.terms', 'locale' => 'pt-PT', 'value' => 'Termos de Serviço', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer.terms', 'locale' => 'en-UK', 'value' => 'Service Terms', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'footer.return_refunds', 'locale' => 'pt-PT', 'value' => 'Política de Devoluções e Reembolsos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer.return_refunds', 'locale' => 'en-UK', 'value' => 'Return and Refunds Policy', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'footer.shipping_policy', 'locale' => 'pt-PT', 'value' => 'Política de Envios', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer.shipping_policy', 'locale' => 'en-UK', 'value' => 'Shipping Policy', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'footer.privacy', 'locale' => 'pt-PT', 'value' => 'Política de Privacidade', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer.privacy', 'locale' => 'en-UK', 'value' => 'Privacy Policy | Política de Privacidade', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'custom.banner.title', 'locale' => 'pt-PT', 'value' => 'Serviços de Produção', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.banner.title', 'locale' => 'en-UK', 'value' => 'Custom Services', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'custom.banner.subtitle', 'locale' => 'pt-PT', 'value' => 'Soluções à medida de impressão 3D.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.banner.subtitle', 'locale' => 'en-UK', 'value' => 'Custom 3D printing solutions.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'custom.banner.button', 'locale' => 'pt-PT', 'value' => 'Preços', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.banner.button', 'locale' => 'en-UK', 'value' => 'Pricing', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'custom.features.title', 'locale' => 'pt-PT', 'value' => 'Opções', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.title', 'locale' => 'en-UK', 'value' => 'Options', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'custom.features.modeling', 'locale' => 'pt-PT', 'value' => 'Modelação 3D', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.modeling', 'locale' => 'en-UK', 'value' => '3D Modeling', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'custom.features.modeling_desc', 'locale' => 'pt-PT', 'value' => 'Modelação, preparação e otimização profissional de modelos 3D para impressão.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.modeling_desc', 'locale' => 'en-UK', 'value' => 'Professional 3D model shaping, preparation and optimization for printing.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'custom.features.materials', 'locale' => 'pt-PT', 'value' => 'Múltiplos Materiais', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.materials', 'locale' => 'en-UK', 'value' => 'Multiple Materials', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'custom.features.materials_desc', 'locale' => 'pt-PT', 'value' => 'Vários materiais e acabamentos para atender às necessidades do projeto.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.materials_desc', 'locale' => 'en-UK', 'value' => 'Various materials and finishes to suit your project needs.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'custom.features.support', 'locale' => 'pt-PT', 'value' => 'Apoio Especializado', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.support', 'locale' => 'en-UK', 'value' => 'Expert Support', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'custom.features.support_desc', 'locale' => 'pt-PT', 'value' => 'Suporte dedicado desde a consulta de design até a entrega final.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.support_desc', 'locale' => 'en-UK', 'value' => 'Dedicated support from design consultation to final delivery.', 'created_at' => $now, 'updated_at' => $now],

            // Modeling bullets
            ['key' => 'custom.features.modeling_b1', 'locale' => 'en-UK', 'value' => 'FreeCad | Fusion | Revit | Archicad', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.modeling_b1', 'locale' => 'pt-PT', 'value' => 'FreeCad | Fusion | Revit | Archicad', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.modeling_b2', 'locale' => 'en-UK', 'value' => 'Blender | Archicad (soon)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.modeling_b2', 'locale' => 'pt-PT', 'value' => 'Blender | Archicad (em breve)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.modeling_b3', 'locale' => 'en-UK', 'value' => 'Check with us other software', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.modeling_b3', 'locale' => 'pt-PT', 'value' => 'Verifique connosco outros softwares', 'created_at' => $now, 'updated_at' => $now],

            // Materials bullets
            ['key' => 'custom.features.materials_b1', 'locale' => 'en-UK', 'value' => 'PLA | PETG | TPU', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.materials_b1', 'locale' => 'pt-PT', 'value' => 'PLA | PETG | TPU', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.materials_b2', 'locale' => 'en-UK', 'value' => 'Translucent Options', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.materials_b2', 'locale' => 'pt-PT', 'value' => 'Opções Translúcidas', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.materials_b3', 'locale' => 'en-UK', 'value' => 'Contact us for others', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.materials_b3', 'locale' => 'pt-PT', 'value' => 'Contacte-nos para outros', 'created_at' => $now, 'updated_at' => $now],

            // Support bullets
            ['key' => 'custom.features.support_b1', 'locale' => 'en-UK', 'value' => 'Architecture Background', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.support_b1', 'locale' => 'pt-PT', 'value' => 'Formação em Arquitetura', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.support_b2', 'locale' => 'en-UK', 'value' => 'IT Background', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.support_b2', 'locale' => 'pt-PT', 'value' => 'Formação em TI', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.support_b3', 'locale' => 'en-UK', 'value' => 'Flexible and Personalized', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.features.support_b3', 'locale' => 'pt-PT', 'value' => 'Flexível e Personalizado', 'created_at' => $now, 'updated_at' => $now],

            // custom.request — 3-service-card section
            ['key' => 'custom.request.title',        'locale' => 'pt-PT', 'value' => 'Preços', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.title',        'locale' => 'en-UK', 'value' => 'Pricing', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.subtitle1',     'locale' => 'pt-PT', 'value' => 'Escolha o serviço que melhor se adapta à sua situação.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.subtitle1',     'locale' => 'en-UK', 'value' => 'Choose the service that best matches your situation.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.subtitle2',     'locale' => 'pt-PT', 'value' => 'Leia atentamente as caracteristicas e informações de cada opção.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.subtitle2',     'locale' => 'en-UK', 'value' => 'Read carefully the features and informations of each option.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.subtitle3',     'locale' => 'pt-PT', 'value' => 'Cada opção abrirá um ticket com o serviço já selecionado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.subtitle3',     'locale' => 'en-UK', 'value' => 'Each option opens a ticket with the right service already selected.', 'created_at' => $now, 'updated_at' => $now],
            
            // Card 1 — R&D
            ['key' => 'custom.request.rnd_tier',     'locale' => 'pt-PT', 'value' => 'I&D + Preparação + Impressão',            'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_tier',     'locale' => 'en-UK', 'value' => 'R&D + Preparation + Print',               'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_title',    'locale' => 'pt-PT', 'value' => 'Tenho uma ideia',                         'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_title',    'locale' => 'en-UK', 'value' => 'I have an idea',                          'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_desc',     'locale' => 'pt-PT', 'value' => 'Tem um conceito mas não tem ficheiro. Nós modelamos, preparamos e imprimimos tudo de raiz com base nas suas especificações.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_desc',     'locale' => 'en-UK', 'value' => 'You have a concept but no file. We model, prepare and print everything from scratch according to your specifications.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_bullet1',  'locale' => 'pt-PT', 'value' => 'Desenvolvimento completo do produto',     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_bullet1',  'locale' => 'en-UK', 'value' => 'Full product development',                'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_bullet2',  'locale' => 'pt-PT', 'value' => 'Orçamento inclui I&D, preparação e impressão', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_bullet2',  'locale' => 'en-UK', 'value' => 'Quote includes R&D, prep & print',        'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_bullet3',  'locale' => 'pt-PT', 'value' => 'Preço baseado na complexidade e tempo de impressão', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_bullet3',  'locale' => 'en-UK', 'value' => 'Price based on complexity + print time', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_attach',   'locale' => 'pt-PT', 'value' => 'Envie-nos fotos, esboços, imagens de referência, ficheiros existentes.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.rnd_attach',   'locale' => 'en-UK', 'value' => 'Send us photos, sketches, reference images, any existing files.', 'created_at' => $now, 'updated_at' => $now],

            // Card 2 — Preparation
            ['key' => 'custom.request.prep_tier',    'locale' => 'pt-PT', 'value' => 'Preparação + Impressão',                  'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_tier',    'locale' => 'en-UK', 'value' => 'Preparation + Print',                     'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_title',   'locale' => 'pt-PT', 'value' => 'Tenho um modelo 3D',                      'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_title',   'locale' => 'en-UK', 'value' => 'I have a 3D model',                       'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_desc',    'locale' => 'pt-PT', 'value' => 'Tem um ficheiro 3D (ex. projeto de arquitetura ou design) mas precisa de ser otimizado e preparado antes da impressão.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_desc',    'locale' => 'en-UK', 'value' => 'You have a 3D file (e.g. an architecture or design project) but it needs to be optimised and prepared before printing.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_bullet1', 'locale' => 'pt-PT', 'value' => 'Preparação: 30€/h (incrementos de 15 min)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_bullet1', 'locale' => 'en-UK', 'value' => 'Preparation: €30/hr (15-min billing)',    'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_bullet2', 'locale' => 'pt-PT', 'value' => 'Impressão: 20€/h (incrementos de 15 min)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_bullet2', 'locale' => 'en-UK', 'value' => 'Print: €20/hr (15-min billing)',   'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_bullet3', 'locale' => 'pt-PT', 'value' => '20% desconto para estudantes',           'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_bullet3', 'locale' => 'en-UK', 'value' => '20% student discount',                  'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_attach',  'locale' => 'pt-PT', 'value' => 'Anexe: ficheiro(s) 3D + escala, cor, especificações de material.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.prep_attach',  'locale' => 'en-UK', 'value' => 'Attach: 3D file(s) + scale, colour, material specs.', 'created_at' => $now, 'updated_at' => $now],

            // Card 3 — Print
            ['key' => 'custom.request.print_tier',    'locale' => 'pt-PT', 'value' => 'Apenas Impressão',                       'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_tier',    'locale' => 'en-UK', 'value' => 'Print only',                             'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_title',   'locale' => 'pt-PT', 'value' => 'Tenho o ficheiro pronto',       'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_title',   'locale' => 'en-UK', 'value' => 'I have a print-ready file',              'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_desc',    'locale' => 'pt-PT', 'value' => 'O seu ficheiro está pronto para impressão. Envie e nós damos orçamento e imprimimos. Ideal também para modelos encontrados online.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_desc',    'locale' => 'en-UK', 'value' => 'Your file is ready to print. Send it over and we will quote and print it. Also ideal for models found online.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_bullet1', 'locale' => 'pt-PT', 'value' => '20€/h · faturação em incrementos de 15 min', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_bullet1', 'locale' => 'en-UK', 'value' => '€20/hr · billed in 15-min sets',         'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_bullet2', 'locale' => 'pt-PT', 'value' => 'PLA incluído · outros materiais sob consulta', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_bullet2', 'locale' => 'en-UK', 'value' => 'PLA included · other materials on request', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_bullet3', 'locale' => 'pt-PT', 'value' => '20% desconto para estudantes',           'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_bullet3', 'locale' => 'en-UK', 'value' => '20% student discount',                  'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_attach',  'locale' => 'pt-PT', 'value' => 'Anexe: ficheiro(s) prontos + escala, cor, material, altura de camada, preenchimento.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.print_attach',  'locale' => 'en-UK', 'value' => 'Attach: print-ready file(s) + scale, colour, material, layer height, infill.', 'created_at' => $now, 'updated_at' => $now],

            // Shared CTA button
            ['key' => 'custom.request.cta_button',   'locale' => 'pt-PT', 'value' => 'Solicitar serviço',                 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'custom.request.cta_button',   'locale' => 'en-UK', 'value' => 'Request service',                   'created_at' => $now, 'updated_at' => $now],

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

            ['key' => 'tickets.email.event.new_message', 'locale' => 'en-UK', 'value' => 'New message', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.event.new_message', 'locale' => 'pt-PT', 'value' => 'Nova mensagem', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.event.created', 'locale' => 'en-UK', 'value' => 'New ticket created', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.event.created', 'locale' => 'pt-PT', 'value' => 'Novo ticket criado', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.event.admin_update', 'locale' => 'en-UK', 'value' => 'Administrative update', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.event.admin_update', 'locale' => 'pt-PT', 'value' => 'Atualização administrativa', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.event.closed', 'locale' => 'en-UK', 'value' => 'Ticket closed', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.event.closed', 'locale' => 'pt-PT', 'value' => 'Ticket fechado', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'tickets.email.event.reopened', 'locale' => 'en-UK', 'value' => 'Ticket reopened', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.email.event.reopened', 'locale' => 'pt-PT', 'value' => 'Ticket reaberto', 'created_at' => $now, 'updated_at' => $now],

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

            ['key' => 'orders.email.event.paid', 'locale' => 'en-UK', 'value' => 'Payment received — your order is now being processed', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.event.paid', 'locale' => 'pt-PT', 'value' => 'Pagamento recebido — o seu pedido está a ser processado', 'created_at' => $now, 'updated_at' => $now],

            // Refund event (used when an order is marked refunded by the payment gateway)
            ['key' => 'orders.email.event.refunded', 'locale' => 'en-UK', 'value' => 'Refund issued', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.event.refunded', 'locale' => 'pt-PT', 'value' => 'Reembolso efetuado', 'created_at' => $now, 'updated_at' => $now],

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

            // Legal pages (Terms & Privacy)
            ['key' => 'legal.terms.title', 'locale' => 'pt-PT', 'value' => 'Termos de Serviço', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.title', 'locale' => 'en-UK', 'value' => 'Service Terms', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.terms.content', 'locale' => 'pt-PT', 'value' => 'Estes Termos de Serviço regulam a utilização do site e dos serviços. Ao criar uma conta, concorda com estes termos.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.content', 'locale' => 'en-UK', 'value' => 'These Terms of Service govern your use of the site and services. By creating an account you agree to these terms.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.terms.section_usage_title', 'locale' => 'pt-PT', 'value' => 'Utilização do Serviço', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.section_usage_title', 'locale' => 'en-UK', 'value' => 'Use of the Service', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.terms.section_usage', 'locale' => 'pt-PT', 'value' => 'Concorda em usar o serviço de acordo com a lei aplicável e a não abusar da plataforma.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.section_usage', 'locale' => 'en-UK', 'value' => 'You agree to use the service in compliance with applicable laws and not to misuse the platform.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.terms.section_limitation_title', 'locale' => 'pt-PT', 'value' => 'Limitação de Responsabilidade', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.section_limitation_title', 'locale' => 'en-UK', 'value' => 'Limitation of Liability', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.terms.section_limitation', 'locale' => 'pt-PT', 'value' => 'A nossa responsabilidade está limitada conforme permitido por lei. Os serviços são fornecidos "tal como estão".', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.section_limitation', 'locale' => 'en-UK', 'value' => 'Our liability is limited as permitted by law. Services are provided "as-is".', 'created_at' => $now, 'updated_at' => $now],

            // new sections added per requirement
            ['key' => 'legal.terms.section_returns_title', 'locale' => 'pt-PT', 'value' => 'Política de Devoluções e Reembolsos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.section_returns_title', 'locale' => 'en-UK', 'value' => 'Return and Refunds Policy', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.section_returns', 'locale' => 'pt-PT', 'value' => 'Descrição da política de devoluções e reembolsos.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.section_returns', 'locale' => 'en-UK', 'value' => 'Description of the return and refunds policy.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.terms.section_shipping_title', 'locale' => 'pt-PT', 'value' => 'Política de Envios', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.section_shipping_title', 'locale' => 'en-UK', 'value' => 'Shipping Policy', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.section_shipping', 'locale' => 'pt-PT', 'value' => 'Descrição da política de envios.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.section_shipping', 'locale' => 'en-UK', 'value' => 'Description of the shipping policy.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.terms.last_updated', 'locale' => 'pt-PT', 'value' => 'Última atualização: Fevereiro de 2026', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.terms.last_updated', 'locale' => 'en-UK', 'value' => 'Last updated: February 2026', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.privacy.title', 'locale' => 'pt-PT', 'value' => 'Política de Privacidade', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.privacy.title', 'locale' => 'en-UK', 'value' => 'Privacy Policy', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.privacy.content', 'locale' => 'pt-PT', 'value' => 'Recolhemos e usamos informação pessoal para prestar os nossos serviços. Respeitamos a sua privacidade e processamos os dados de acordo com a lei aplicável.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.privacy.content', 'locale' => 'en-UK', 'value' => 'We collect and use personal information to provide our services. We respect your privacy and process data according to applicable law.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.privacy.section_data_title', 'locale' => 'pt-PT', 'value' => 'Dados Que Recolhemos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.privacy.section_data_title', 'locale' => 'en-UK', 'value' => 'Data We Collect', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.privacy.section_data', 'locale' => 'pt-PT', 'value' => 'Recolhemos dados fornecidos por si (detalhes da conta, encomendas) e dados técnicos (cookies, analytics).', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.privacy.section_data', 'locale' => 'en-UK', 'value' => 'We collect data you provide (account details, order information) and technical data (cookies, analytics).', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.privacy.section_usage_title', 'locale' => 'pt-PT', 'value' => 'Como Usamos os Dados', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.privacy.section_usage_title', 'locale' => 'en-UK', 'value' => 'How We Use Data', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.privacy.section_usage', 'locale' => 'pt-PT', 'value' => 'Usamos dados pessoais para processar encomendas, comunicar consigo e melhorar o serviço. Não vendemos dados pessoais a terceiros.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.privacy.section_usage', 'locale' => 'en-UK', 'value' => 'Personal data is used to process orders, communicate with you, and improve the service. We do not sell personal data to third parties.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'legal.privacy.last_updated', 'locale' => 'pt-PT', 'value' => 'Última atualização: Fevereiro de 2026', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'legal.privacy.last_updated', 'locale' => 'en-UK', 'value' => 'Last updated: February 2026', 'created_at' => $now, 'updated_at' => $now],

            // Contact form fields
            ['key' => 'contact.name', 'locale' => 'pt-PT', 'value' => 'Nome', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.name', 'locale' => 'en-UK', 'value' => 'Name', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.email', 'locale' => 'pt-PT', 'value' => 'Email', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.email', 'locale' => 'en-UK', 'value' => 'Email', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.message', 'locale' => 'pt-PT', 'value' => 'Mensagem', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.message', 'locale' => 'en-UK', 'value' => 'Message', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.send', 'locale' => 'pt-PT', 'value' => 'Enviar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.send', 'locale' => 'en-UK', 'value' => 'Send', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'contact.success_message', 'locale' => 'pt-PT', 'value' => 'Obrigado pela sua mensagem! Entraremos em contacto em breve.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.success_message', 'locale' => 'en-UK', 'value' => 'Thank you for your message! We will get back to you soon.', 'created_at' => $now, 'updated_at' => $now],

            // Flash shown when validation fails server-side (e.g. invalid email)
            ['key' => 'contact.validation_failed', 'locale' => 'pt-PT', 'value' => 'Por favor corrija os erros abaixo e tente novamente.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact.validation_failed', 'locale' => 'en-UK', 'value' => 'Please correct the errors below and try again.', 'created_at' => $now, 'updated_at' => $now],

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

            // Register acceptance validation messages
            ['key' => 'validation.terms_required', 'locale' => 'pt-PT', 'value' => 'Tem de aceitar os Termos de Serviço.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.terms_required', 'locale' => 'en-UK', 'value' => 'You must accept the Terms of Service.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'validation.privacy_required', 'locale' => 'pt-PT', 'value' => 'Tem de aceitar a Política de Privacidade.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'validation.privacy_required', 'locale' => 'en-UK', 'value' => 'You must accept the Privacy Policy.', 'created_at' => $now, 'updated_at' => $now],

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

            // Error page translations
            ['key' => 'error.back_home', 'locale' => 'pt-PT', 'value' => 'Voltar para a página inicial', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'error.back_home', 'locale' => 'en-UK', 'value' => 'Back to home', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'error.contact_support', 'locale' => 'pt-PT', 'value' => 'Se o problema persistir, contacte-nos em :email', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'error.contact_support', 'locale' => 'en-UK', 'value' => 'If the problem persists, please contact us at :email', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'error.404.title', 'locale' => 'pt-PT', 'value' => 'Página não encontrada', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'error.404.title', 'locale' => 'en-UK', 'value' => 'Page not found', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'error.404.message', 'locale' => 'pt-PT', 'value' => 'Não encontramos a página que procura.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'error.404.message', 'locale' => 'en-UK', 'value' => 'We can’t find the page you’re looking for.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'error.500.title', 'locale' => 'pt-PT', 'value' => 'Ocorreu um erro', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'error.500.title', 'locale' => 'en-UK', 'value' => 'Something went wrong', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'error.500.message', 'locale' => 'pt-PT', 'value' => 'Lamentamos — ocorreu um erro no nosso lado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'error.500.message', 'locale' => 'en-UK', 'value' => 'Sorry — something went wrong on our end.', 'created_at' => $now, 'updated_at' => $now],

            // Checkout — payment gateway disabled
            ['key' => 'checkout.gateways.disabled', 'locale' => 'pt-PT', 'value' => 'O sistema de pagamento está temporariamente indisponível — por favor verifique os detalhes do seu pedido num momento e tente novamente.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.gateways.disabled', 'locale' => 'en-UK', 'value' => 'Payment system is temporarily unavailable — please check your order details in a moment and try again.', 'created_at' => $now, 'updated_at' => $now],

            // Checkout — address form validation (max-length & format messages)
            ['key' => 'checkout.validation.title_max', 'locale' => 'pt-PT', 'value' => 'O título da morada é demasiado longo.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.title_max', 'locale' => 'en-UK', 'value' => 'Address title is too long.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.nif_max', 'locale' => 'pt-PT', 'value' => 'O NIF/NIT é demasiado longo.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.nif_max', 'locale' => 'en-UK', 'value' => 'NIF/VAT number is too long.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.address_line_1_max', 'locale' => 'pt-PT', 'value' => 'A morada é demasiado longa.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.address_line_1_max', 'locale' => 'en-UK', 'value' => 'Address is too long.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.address_line_2_invalid', 'locale' => 'pt-PT', 'value' => 'O formato da linha 2 da morada é inválido.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.address_line_2_invalid', 'locale' => 'en-UK', 'value' => 'Address line 2 format is invalid.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.address_line_2_max', 'locale' => 'pt-PT', 'value' => 'A linha 2 da morada é demasiado longa.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.address_line_2_max', 'locale' => 'en-UK', 'value' => 'Address line 2 is too long.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.postal_code_max', 'locale' => 'pt-PT', 'value' => 'O código postal é demasiado longo.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.postal_code_max', 'locale' => 'en-UK', 'value' => 'Postal code is too long.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.city_max', 'locale' => 'pt-PT', 'value' => 'O nome da cidade é demasiado longo.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.city_max', 'locale' => 'en-UK', 'value' => 'City name is too long.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'checkout.validation.country_invalid', 'locale' => 'pt-PT', 'value' => 'Por favor selecione um país válido.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'checkout.validation.country_invalid', 'locale' => 'en-UK', 'value' => 'Please select a valid country.', 'created_at' => $now, 'updated_at' => $now],

            // Image gallery component — accessibility labels
            ['key' => 'gallery.prev_image', 'locale' => 'pt-PT', 'value' => 'Imagem anterior', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'gallery.prev_image', 'locale' => 'en-UK', 'value' => 'Previous image', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'gallery.next_image', 'locale' => 'pt-PT', 'value' => 'Imagem seguinte', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'gallery.next_image', 'locale' => 'en-UK', 'value' => 'Next image', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'gallery.view_image', 'locale' => 'pt-PT', 'value' => 'Ver imagem', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'gallery.view_image', 'locale' => 'en-UK', 'value' => 'View image', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'gallery.prev_thumbnails', 'locale' => 'pt-PT', 'value' => 'Miniaturas anteriores', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'gallery.prev_thumbnails', 'locale' => 'en-UK', 'value' => 'Previous thumbnails', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'gallery.next_thumbnails', 'locale' => 'pt-PT', 'value' => 'Miniaturas seguintes', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'gallery.next_thumbnails', 'locale' => 'en-UK', 'value' => 'Next thumbnails', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'gallery.lightbox', 'locale' => 'pt-PT', 'value' => 'Visualizador de imagens', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'gallery.lightbox', 'locale' => 'en-UK', 'value' => 'Image viewer', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'gallery.zoom_in', 'locale' => 'pt-PT', 'value' => 'Ampliar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'gallery.zoom_in', 'locale' => 'en-UK', 'value' => 'Zoom in', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'gallery.zoom_out', 'locale' => 'pt-PT', 'value' => 'Reduzir', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'gallery.zoom_out', 'locale' => 'en-UK', 'value' => 'Zoom out', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'gallery.zoom_reset', 'locale' => 'pt-PT', 'value' => 'Repor zoom', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'gallery.zoom_reset', 'locale' => 'en-UK', 'value' => 'Reset zoom', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'gallery.close', 'locale' => 'pt-PT', 'value' => 'Fechar', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'gallery.close', 'locale' => 'en-UK', 'value' => 'Close', 'created_at' => $now, 'updated_at' => $now],

            // Orders email — shipping line label
            ['key' => 'orders.email.shipping_label', 'locale' => 'pt-PT', 'value' => 'Envio', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'orders.email.shipping_label', 'locale' => 'en-UK', 'value' => 'Shipping', 'created_at' => $now, 'updated_at' => $now],

            // Profile — data deletion & social auth
            ['key' => 'profile.deletion_link_sent', 'locale' => 'pt-PT', 'value' => 'Foi enviado um link de eliminação da conta para o seu endereço de e-mail.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.deletion_link_sent', 'locale' => 'en-UK', 'value' => 'A deletion link has been sent to your email address.', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'profile.provider_disabled', 'locale' => 'pt-PT', 'value' => 'O início de sessão social está atualmente desativado.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'profile.provider_disabled', 'locale' => 'en-UK', 'value' => 'Social sign-in is currently disabled.', 'created_at' => $now, 'updated_at' => $now],

            // Store — product card favourites buttons
            ['key' => 'store.add_to_favorites', 'locale' => 'pt-PT', 'value' => 'Adicionar aos favoritos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.add_to_favorites', 'locale' => 'en-UK', 'value' => 'Add to favourites', 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'store.remove_from_favorites', 'locale' => 'pt-PT', 'value' => 'Remover dos favoritos', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'store.remove_from_favorites', 'locale' => 'en-UK', 'value' => 'Remove from favourites', 'created_at' => $now, 'updated_at' => $now],

            // Tax — cart & checkout note
            ['key' => 'tax.included_in_price', 'locale' => 'pt-PT', 'value' => 'Todos os impostos estão incluídos no preço', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tax.included_in_price', 'locale' => 'en-UK', 'value' => 'All taxes are included in the price', 'created_at' => $now, 'updated_at' => $now],

            // Tickets — message send failure
            ['key' => 'tickets.message_failed', 'locale' => 'pt-PT', 'value' => 'Não foi possível guardar a mensagem. Por favor tente novamente.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tickets.message_failed', 'locale' => 'en-UK', 'value' => 'Failed to save message. Please try again.', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('static_translations')->upsert(
            // Add context to each row (used on INSERT only — see update columns below)
            array_map(fn($r) => $r + ['context' => $getContext($r['key'])], $rows),
            // Unique key for conflict detection
            ['key', 'locale'],
            // On conflict only update value & timestamps; context edits made via admin are preserved
            ['value', 'updated_at']
        );

        // Backfill context for rows that don't have one yet (initial migration or new keys).
        // Rows that already have a context (set by admin or a previous seed) are untouched.
        $uniqueKeys = array_unique(array_column($rows, 'key'));
        foreach ($uniqueKeys as $key) {
            $context = $getContext($key);
            if ($context !== '') {
                DB::table('static_translations')
                    ->where('key', $key)
                    ->whereNull('context')
                    ->update(['context' => $context]);
            }
        }

        // Ensure any cached static translations are refreshed immediately
        \Illuminate\Support\Facades\Cache::forget('static_translations_all');
    }
}
