<?php

namespace Webkul\Marketplace\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('core_config')->insert([
            [
                'code'         => 'marketplace.settings.general.status',
                'value'        => 0,
                'channel_code' => null,
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.general.seller_approval_required',
                'value'        => 1,
                'channel_code' => null,
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.general.product_approval_required',
                'value'        => 1,
                'channel_code' => null,
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.general.can_create_invoice',
                'value'        => 1,
                'channel_code' => null,
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.general.can_create_shipment',
                'value'        => 1,
                'channel_code' => null,
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.general.can_cancel_order',
                'value'        => 1,
                'channel_code' => null,
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.general.commission_per_unit',
                'value'        => '10',
                'channel_code' => 'default',
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.landing_page.banner_title',
                'value'        => 'Create a Velocity seller account',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.banner_title',
                'value'        => 'قم بإنشاء حساب بائع السرعة',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.banner_description',
                'value'        => 'Velocity can be a great platform for selling your products to new-age businesses in India. By following these tips, you can set yourself up for success as a Velocity seller!',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.banner_description',
                'value'        => 'يمكن أن تكون Velocity منصة رائعة لبيع منتجاتك للشركات الناشئة في الهند. باتباع هذه النصائح، يمكنك إعداد نفسك للنجاح كبائع Velocity!',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.banner_btn_title',
                'value'        => 'Open Store',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.banner_btn_title',
                'value'        => 'افتح المتجر',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.banner_image',
                'value'        => 'configuration/DyBS0HBGRGW147DvZKTdgdiSiaWf6kHPgLK6qQFm.webp',
                'channel_code' => 'default',
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.landing_page.community_count',
                'value'        => '1000',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.community_count',
                'value'        => '1000',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.business_hour',
                'value'        => '24x7',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.business_hour',
                'value'        => '24x7',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.payment_duration',
                'value'        => '15',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.payment_duration',
                'value'        => '15',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.serviceable_pincode',
                'value'        => '1000',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.serviceable_pincode',
                'value'        => '1000',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_title',
                'value'        => 'Why do sellers love to sell on velocity?',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_title',
                'value'        => 'لماذا يحب البائعون البيع على السرعة؟',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_description',
                'value'        => 'sellers love high-velocity platforms because they offer the potential for increased sales, better exposure, and an efficient selling experience that can contribute to business growth and success.',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_description',
                'value'        => 'يحب البائعون المنصات عالية السرعة لأنها توفر إمكانية زيادة المبيعات والتعرض الأفضل وتجربة بيع فعالة يمكن أن تساهم في نمو الأعمال ونجاحها.',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_image',
                'value'        => 'configuration/A9BsGYfcgCioTpGbmvnuSDR2BNIGf9hxTxpo1x3B.webp',
                'channel_code' => 'default',
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box1_icon',
                'value'        => 'configuration/IWddepraaapncnfs8BxLVCPFaFv2joaCbNxRoKpQ.svg',
                'channel_code' => 'default',
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box1_title',
                'value'        => 'Increased Sales Opportunities',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box1_title',
                'value'        => 'زيادة فرص المبيعات',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box1_desc',
                'value'        => 'sellers love high-velocity platforms because they offer the potential for increased sales, better exposure, and an efficient selling experience that can contribute to business growth and success.',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box1_desc',
                'value'        => 'يحب البائعون المنصات عالية السرعة لأنها توفر إمكانية زيادة المبيعات والتعرض الأفضل وتجربة بيع فعالة يمكن أن تساهم في نمو الأعمال ونجاحها.',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box2_icon',
                'value'        => 'configuration/ffSBdy4Bsh3crYXSMMxoQEwBsf7jnEA4cRBJzLpC.svg',
                'channel_code' => 'default',
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box2_title',
                'value'        => 'Faster Turnaround',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box2_title',
                'value'        => 'تحول أسرع',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box2_desc',
                'value'        => 'Products tend to sell more quickly on high-velocity platforms, leading to faster inventory turnover and revenue generation for sellers.',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box2_desc',
                'value'        => 'تميل المنتجات إلى البيع بسرعة أكبر على منصات عالية السرعة، مما يؤدي إلى دوران أسرع للمخزون وتوليد الإيرادات للبائعين.',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box3_icon',
                'value'        => 'configuration/LP9XZ9XfXPMoTq3rWra0ndEngVUdAXDgGDUvKUuK.svg',
                'channel_code' => 'default',
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box3_title',
                'value'        => 'Enhanced Visibility',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box3_title',
                'value'        => 'تعزيز الرؤية',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box3_desc',
                'value'        => 'These platforms offer greater visibility and exposure for seller products, resulting in more views and potential sales.',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box3_desc',
                'value'        => 'توفر هذه المنصات رؤية وتعرضًا أكبر لمنتجات البائع، مما يؤدي إلى المزيد من المشاهدات والمبيعات المحتملة.',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box4_icon',
                'value'        => 'configuration/rRXj8cIvGt1ym9DKFuCZoEgrPAT01lfPG7ZfilLk.png',
                'channel_code' => 'default',
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box4_title',
                'value'        => 'Efficiency and Convenience',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box4_title',
                'value'        => 'الكفاءة والراحة',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box4_desc',
                'value'        => 'High-velocity platforms often have streamlined processes and tools that make selling more efficient, saving sellers time and effort in managing their online businesses.',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.feature_box4_desc',
                'value'        => 'غالبًا ما تحتوي المنصات عالية السرعة على عمليات وأدوات مبسطة تجعل البيع أكثر كفاءة، مما يوفر الوقت والجهد للبائعين في إدارة أعمالهم التجارية عبر الإنترنت.',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_title',
                'value'        => 'Start your journey on velocity',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_title',
                'value'        => 'ابدأ رحلتك بالسرعة',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_description',
                'value'        => 'Start selling with large customers around the world, round the clock.',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_description',
                'value'        => 'ابدأ البيع مع كبار العملاء حول العالم، على مدار الساعة.',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step1_icon',
                'value'        => 'configuration/eXlLe5OmdaQfWpV6i4kDi00ubgeVzdl7tyivVCw6.svg',
                'channel_code' => 'default',
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step1_title',
                'value'        => 'Become Seller',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step1_title',
                'value'        => 'كن بائعًا',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step1_desc',
                'value'        => 'Setup your store, with all the basic information.',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step1_desc',
                'value'        => 'قم بإعداد متجرك بكل المعلومات الأساسية.',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step2_icon',
                'value'        => 'configuration/wePx7Gu2ZB0yNxaVTnQVfGO5e9SvkRKfSAmBRHDc.svg',
                'channel_code' => 'default',
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step2_title',
                'value'        => 'List Products',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step2_title',
                'value'        => 'قائمة المنتجات',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step2_desc',
                'value'        => 'List the products in store for the customer to view and purchase.',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step2_desc',
                'value'        => 'قم بإدراج المنتجات الموجودة في المتجر ليتمكن العميل من عرضها وشرائها.',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step3_icon',
                'value'        => 'configuration/w9ovY22tcbqM99HsnVd4fbFcCxkssF54E2Q5w7zr.svg',
                'channel_code' => 'default',
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step3_title',
                'value'        => 'Receive Order',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step3_title',
                'value'        => 'إستقبال الأوامر',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step3_desc',
                'value'        => 'Receive orders from a wide range of customers around the world.',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step3_desc',
                'value'        => 'تلقي الطلبات من مجموعة واسعة من العملاء حول العالم.',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step4_icon',
                'value'        => 'configuration/0bVS9AXCfEFE3n57Zx2axZbzdCYvJ8HSGVc3LlVn.svg',
                'channel_code' => 'default',
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step4_title',
                'value'        => 'Product Delivery',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step4_title',
                'value'        => 'ايصال المنتج',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step4_desc',
                'value'        => 'Once the product is delivered and payment received',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step4_desc',
                'value'        => 'بمجرد تسليم المنتج واستلام المبلغ',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step5_icon',
                'value'        => 'configuration/rYld3h3AMAQcHFWWQkI7DwrCoOD1b2cR4n9U7bZW.svg',
                'channel_code' => 'default',
                'locale_code'  => null,
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step5_title',
                'value'        => 'Payment',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step5_title',
                'value'        => 'قسط',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step5_desc',
                'value'        => 'Payment request, get payment after the commission cut.',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ], [
                'code'         => 'marketplace.settings.landing_page.journey_step5_desc',
                'value'        => 'طلب الدفع، الحصول على الدفع بعد خفض العمولة.',
                'channel_code' => 'default',
                'locale_code'  => 'ar',
            ],
        ]);
    }
}
