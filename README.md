# 🌤️ Hava Durumu TR

Bu proje, Türkiye genelindeki şehirler için güncel ve 7 günlük hava durumu tahminlerini sağlayan bir PHP tabanlı web uygulamasıdır. Modern ve responsive tasarımıyla kullanıcı dostu bir arayüz sunar.

## Özellikler

- Tüm Türkiye şehirleri için hava durumu desteği.
- Güncel sıcaklık, minimum, maksimum, gece sıcaklığı ve nem bilgisi.
- 7 günlük hava durumu tahmini.
- Farklı tema seçenekleri: Varsayılan, Okyanus, Gün Batımı, Orman, Karanlık.
- SEO ve sosyal medya meta etiketleri (Open Graph, Twitter Card, JSON-LD schema).
- Lazy loading ve performans optimizasyonları.
- Responsive ve modern tasarım.

## Kurulum

1. PHP 7.4 veya üzeri bir sunucuya ihtiyacınız vardır.
2. Proje dosyalarını sunucunuza yükleyin.
3. Tarayıcınızdan proje dizinine gidin. Örnek:  
https://example.com/index.php?il=ankara

4. URL parametresi `?il=şehir` ile farklı şehirleri görüntüleyebilirsiniz. Örnek:  
?il=istanbul

## Kullanım

- Şehir seçmek için açılır menüyü kullanın.
- Temayı değiştirmek için tema butonlarını veya `Alt + 1-5` klavye kısayollarını kullanabilirsiniz.
- Hava durumu verileri her 30 dakikada bir cache üzerinden güncellenir.
- Hata durumunda tüm API endpoint’leri denenir ve kullanıcıya bilgi mesajı gösterilir.

## Kullanım Koşulları ve Lisans Hakkı

- Bu yazılımın kullanımı, kopyalanması ve dağıtımı, bu depoda bulunan LICENSE.txt dosyasında belirtilen katı kurallara ve şartlara tabidir.
- Kullanıma başlamadan önce, özellikle yazılımın ticari olmayan yapısını ve Lisans Sahibinin (Egemen KEYDAL) API/Hizmetleri istediği zaman sonlandırma hakkını içeren lisans şartlarını dikkatle okuyunuz.
---------
- The use, copying, and distribution of this software are strictly subject to the rules and conditions set forth in the LICENSE.txt file found in this repository.
- Before proceeding, please carefully review the license terms, particularly regarding the non-commercial nature of the software and the Licensor's (Egemen KEYDAL) right to terminate API/Services at any time.

## Geliştirici

**Egemen KEYDAL** – [egemenkeydal.com](https://www.egemenkeydal.com/)