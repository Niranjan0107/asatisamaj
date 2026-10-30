# Samanvay WordPress Theme

Samanvay is a custom classic WordPress theme for Indian social, welfare, community, NGO, and society websites.

## Install

1. Copy or upload this folder to `wp-content/themes/samanvay`.
2. Activate **Samanvay** in Appearance → Themes.
3. Add a site title, tagline, logo, and menus in Appearance → Customize.
4. For a content-led homepage, create a page and set it as the static front page under Settings → Reading.

## Customization

- **Appearance → Customize → Colors** controls the primary, secondary, body text, and accent colors.
- **Appearance → Customize → Typography** controls body and heading font families, body font size, and line height.
- The font choices use privacy-friendly local/system font stacks and do not require third-party font requests.

The homepage works immediately with built-in programme and impact content. The latest three posts appear automatically when posts are available.

## Member registration

The theme creates a **Member Registration** page automatically and routes the header and footer registration buttons to it. Configure the fee, duration, and payment QR image under **Appearance → Theme Settings → Membership**.

Member profiles and payment records are stored in the WordPress-prefixed `samanvay_members` and `samanvay_member_payments` tables. Review screenshots under **Users → Community Members**. Approving a payment activates the account for the configured duration.

The theme also creates **Member Login** (`/member-login/`) and **Member Dashboard** (`/member-dashboard/`) pages. Pending and expired members can log in, edit their profile, submit a missed payment or future renewal, and review all payment statuses. Only active, unexpired members appear in the public directory.

Password recovery stays on the frontend using `/member-forgot-password/` and `/member-reset-password/`. Reset links use WordPress security keys internally but never send members through the default WordPress password-recovery pages.

India State → District → Sub-district data is bundled in `assets/data-india-locations.json` from the MIT-licensed India-Locations-Dataset project. Its license notice is included beside the data file.

Use `[samanvay_active_members]` on any page to display the public active-member directory. Optional attributes include `view` (`grid` or `list`), `per_page`, `columns`, and `title`. Examples: `[samanvay_active_members view="grid" columns="4"]` and `[samanvay_active_members view="list" per_page="20"]`.
