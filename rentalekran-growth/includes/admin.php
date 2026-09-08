<?php
defined('ABSPATH') || exit;
add_action('admin_menu', function () { add_options_page('Rental Ekran', 'Rental Ekran', 'manage_options', 'rentalekran', 'rle_admin_page'); });
add_action('admin_init', function () {
    register_setting('rle', 'rle_settings', array('sanitize_callback'=>'rle_sanitize_settings'));
});
function rle_sanitize_settings($input) {
    if (!is_array($input)) return rle_settings();
    $old = rle_settings();
    $email = sanitize_email(isset($input['email']) && is_scalar($input['email']) ? $input['email'] : '');
    if (!is_email($email)) { add_settings_error('rle_settings','email','Geçerli bir e-posta adresi girin.'); $email = $old['email']; }
    $wa = preg_replace('/\D/', '', isset($input['whatsapp']) && is_scalar($input['whatsapp']) ? $input['whatsapp'] : '');
    if (!preg_match('/^[1-9][0-9]{7,14}$/', $wa)) { add_settings_error('rle_settings','whatsapp','WhatsApp numarası ülke koduyla 8–15 rakam olmalıdır.'); $wa=$old['whatsapp']; }
    $phone = sanitize_text_field(isset($input['phone']) && is_scalar($input['phone']) ? $input['phone'] : '');
    if (strlen(preg_replace('/\D/','',$phone)) < 8) $phone=$old['phone'];
    return array('email'=>$email,'whatsapp'=>$wa,'phone'=>$phone,'live_enabled'=>!empty($input['live_enabled']),
        'address'=>sanitize_text_field(isset($input['address']) && is_scalar($input['address']) ? $input['address'] : $old['address']),
        'business_mode'=>isset($input['business_mode']) && in_array($input['business_mode'],array('project','rental','both'),true) ? $input['business_mode'] : 'project');
}
function rle_admin_page() {
    if (!current_user_can('manage_options')) return;
    $s=rle_settings();
    echo '<div class="wrap"><h1>Rental Ekran</h1><p>'.(rle_live()?'Yeni site yayında.':'Yeni site yalnızca yönetici önizlemesinde; ziyaretçiler eski siteyi görüyor.').'</p><p><a class="button" target="_blank" rel="noopener" href="'.esc_url(add_query_arg('rle_preview','1',home_url('/'))).'">Yönetici önizlemesini aç</a></p><p>Eski içerikler ve tema kayıtları korunur. Eklentiyi kapatarak önceki sunuma dönebilirsiniz.</p>';
    if (rle_has_seo_plugin()) echo '<div class="notice notice-warning"><p>Başka bir SEO eklentisi bulundu. Çift meta/şema üretmemek için Rental Ekran meta çıktısı durduruldu. Bu paket ile aynı anda tek SEO yayıncısı kullanın; sanal sayfaların sitemap ve canonical ayarlarını tekrar doğrulayın.</p></div>';
    if (!get_option('blog_public')) echo '<div class="notice notice-warning"><p>WordPress arama motoru görünürlüğü kapalı. Hazırlık ortamında doğrudur; canlıda Ayarlar → Okuma üzerinden kontrol edin.</p></div>';
    settings_errors('rle_settings');
    echo '<form action="options.php" method="post">'; settings_fields('rle');
    echo '<table class="form-table">';
    echo '<tr><th>Yayın durumu</th><td><label><input type="checkbox" name="rle_settings[live_enabled]" value="1" '.checked(rle_live(),true,false).'> Yeni siteyi ziyaretçilere aç</label><p class="description">Önizleme kontrolü tamamlandıktan sonra açın. Kapalıyken yalnızca oturum açmış yöneticiler rle_preview=1 ile yeni sayfaları görebilir.</p></td></tr>';
    foreach (array('phone'=>'Telefon','whatsapp'=>'WhatsApp (ülke koduyla)','email'=>'E-posta','address'=>'Görünür adres') as $key=>$label) {
        echo '<tr><th><label for="rle-'.esc_attr($key).'">'.esc_html($label).'</label></th><td><input class="regular-text" required id="rle-'.esc_attr($key).'" name="rle_settings['.esc_attr($key).']" type="'.($key==='email'?'email':'text').'" value="'.esc_attr($s[$key]).'"></td></tr>';
    }
    echo '<tr><th><label for="rle-mode">Teklif odağı</label></th><td><select id="rle-mode" name="rle_settings[business_mode]">';
    foreach(array('project'=>'Satış / proje','rental'=>'Kiralama','both'=>'Satış ve kiralama') as $v=>$label) echo '<option value="'.esc_attr($v).'" '.selected($s['business_mode'],$v,false).'>'.esc_html($label).'</option>';
    echo '</select></td></tr></table>'; submit_button('Kaydet'); echo '</form>';
    echo '<h2>Yayına alma kontrolü</h2><p>Gerçek telefon ve WhatsApp hesabını doğrulayın. Tam dosya + veritabanı yedeği alın. Tüm önbellekleri temizleyin. Kullanılmayan önbellek eklentilerini hazırlık ortamında test ederek azaltın. DNS, PHP ve sunucu ayarları bu eklenti tarafından değiştirilmez.</p>';
    echo '<p><a href="'.esc_url(home_url('/wp-sitemap.xml')).'">XML site haritası</a> · <a href="'.rle_link('iletisim').'">Teklif akışı</a></p></div>';
}
