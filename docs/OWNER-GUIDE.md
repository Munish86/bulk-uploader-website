# Bulk Product Uploader website: owner guide (Hinglish)

Ye WordPress website Bulk Product Uploader app ki marketing ke liye hai. Abhi ye sirf tumhare computer par chalti hai.
Reorder Desk website jaisi hi bani hai, to wahan jo seekha woh yahan bhi kaam aayega.

## 1. Site kholna

1. XAMPP Control Panel kholo, **Apache** aur **MySQL** dono Start karo.
2. Browser mein: **http://localhost:8089** (website)
3. Admin panel: **http://localhost:8089/wp-admin/**
   - User aur password: `bulk-uploader-website/local-login.txt` mein hain. Ye file kisi ko mat bhejna.

Reorder Desk site (http://localhost:8088) bhi saath mein chalti rahegi; dono alag hain.
Local site thodi slow khulti hai (OPcache band hai); fast karne ka tareeka Reorder Desk guide ke section 1 mein hai.

## 2. Website mein kya hai

| Page | Address |
|---|---|
| Home | `/` |
| Features | `/features/` |
| How it works (5 steps) | `/how-it-works/` |
| Pricing (Free / Pro / Unlimited) | `/pricing/` |
| Shopify CSV import se comparison (SEO page) | `/shopify-csv-import-alternative/` |
| Help centre | `/help/` |
| Early access (form) | `/early-access/` |
| Contact (form) | `/contact/` |
| Privacy policy, Terms | `/privacy-policy/`, `/terms/` |
| Blog (4 posts) | `/blog/` |

Screenshots asli app ke hain (app ke browser test se liye gaye), koi nakli design nahi.

## 3. Text ya page badalna

- **Koi page:** wp-admin > Pages > page par click > text par click karke badlo > **Save**.
- **Header / footer:** wp-admin > Appearance > Editor > Patterns > Template parts > Header ya Footer.
- **Naya section daalna:** page mein `+` > Patterns > "Bulk Product Uploader" (CTA band, feature cards, FAQ,
  screenshot ke saath text).
- **Price badalna:** Home aur Pricing dono pages par "$19" (Pro) aur "$49" (Unlimited) likha hai. Ye abhi
  **placeholder** hain. Price pakki ho jaye to Claude ko batao, woh website aur app dono jagah badal dega.

## 4. Naya blog post

1. wp-admin > Posts > Add New.
2. Title, text likho. Right side: **Category** (Guides), **Featured image** (cover, 1600x900),
   **Excerpt** (2 line summary: blog list mein aur post ke title ke neeche dikhti hai).
3. Neeche Yoast box mein "Meta description" (Google mein dikhti hai, ~155 characters).
4. **Publish**.

Achhe topics (log Google par ye search karte hain): "Shopify bulk edit products", "Shopify import products from
Excel", "Shopify bulk update prices", "Shopify product CSV template", "Shopify bulk SEO titles", "Shopify variants CSV",
"Google Sheets to Shopify".

## 5. Form entries (early access, contact)

- wp-admin > **Leads**: saari entries; "Download CSV" se Excel mein.
- Early access form mein log batate hain: abhi products kaise daalte hain, aur store mein kitne products hain. Isse
  pata chalega ki kaun bada customer ban sakta hai.
- Local computer se email nahi jaati, par entry hamesha Leads mein save hoti hai.
- Settings > **Bulk Product Uploader site**: support email aur "new sign-up kis email par aaye" yahan bharo.

## 6. App live hone ke din (sirf 1 setting)

Settings > Bulk Product Uploader site > **App Store URL** mein app ka link daalo
(jaise `https://apps.shopify.com/...`) > Save. Website ke saare "Get early access" buttons apne aap
**"Install free on Shopify"** ban jayenge aur App Store par le jayenge. Phir:
- Home ke FAQ "When can I install it?" aur `/early-access/` page ka text badal do (ya Claude se karwa lo).
- Leads > Download CSV, aur sabko ek email bhejo ki app live hai (privacy policy mein yahi vaada hai: sirf ek email).

## 7. Website live karna (domain + hosting)

Abhi site sirf tumhare PC par hai; Google aur customers ise nahi dekh sakte.

1. **Domain lo** (~₹800-1,200/saal). Is naam ki koi aur app ya website nahi mili (15 Oct 2026 ko search kiya), par
   `bulkproductuploader.com` free hai ya nahi, ye domain khareedte waqt dekhna. Na mile to `getbulkuploader.com` jaisa
   koi naam.
2. **Hosting:** koi bhi WordPress hosting jismein PHP 8.2+ aur MySQL ho, aur SSL (https) free mile. Ek hi hosting par
   dono websites (ye aur Reorder Desk) rakh sakte ho. Site domain ke root par honi chahiye (jaise `bulkuploader.xyz`,
   ya `www.` / koi subdomain); kisi folder mein (`domain.com/site/`) nahi, warna links toot jayenge.
3. **Shift karna:** local wp-admin mein plugin **All-in-One WP Migration** install karo > Export > File. Hosting par
   naya WordPress install karo, wahi plugin daalo > Import > wo file. (Ya Claude ko hosting ka access do, Claude kar dega.)
4. Live site par: Settings > Permalinks > Save (ek baar). Settings > Bulk Product Uploader site mein emails bharo.
5. Emails ke liye plugin **WP Mail SMTP** (hosting ka mailbox ya Resend), taaki form entries email par aayein.
6. Google Search Console mein site add karo aur sitemap do: `https://<domain>/sitemap_index.xml`.
7. App Store listing mein privacy aur support link website ke `/privacy-policy/` aur `/help/` par de sakte ho.

## 8. Abhi tumhe check karna hai

- **Prices:** Pro $19/month (2,000 products) aur Unlimited $49/month theek hain? Free trial dena hai? Kitne din?
- Contact page par "1 working day mein reply" ka vaada theek hai?
- Early access page par "pehli sheet set karne mein madad" ka vaada theek hai?
- Privacy policy: early-access emails "app live hone ke 12 mahine mein delete" theek hai? Company / legal naam aur
  address daalna hai to batao.
- Terms: kis desh ka law lagega (India?) batao, Claude add kar dega.
