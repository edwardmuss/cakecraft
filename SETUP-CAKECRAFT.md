# Cakecraft Pastry Academy - setup checklist

## First install
1. `composer install` and `npm install` (then `npm run build` if you use Vite assets).
2. Copy `.env.example` to `.env`, then run `php artisan key:generate`.
3. Set the database in `.env` (`DB_DATABASE=cakecraftpastry`, user, password).
4. Fill in the business details in `.env`: `CAKECRAFT_NAME`, `CAKECRAFT_PHONE`, `CAKECRAFT_WHATSAPP` (international format, no +), `CAKECRAFT_ADDRESS`.
5. Fill in the SMS gateway: `SMS_ENDPOINT`, `SMS_API_KEY`, `SMS_SENDER`.
6. `php artisan migrate:fresh --seed` (creates tables, the first branch, roles and permissions).
7. `php artisan cakecraft:create-admin` (public registration is disabled, so this is how admins are created).
8. `php artisan storage:link` (needed for uploaded cake photos to show).
9. Optional sample catalogue: `php artisan db:seed --class=CakecraftDemoSeeder` (placeholder prices - edit them in Cakes).

## Running
- `php artisan serve`, then open `/` for the public cake site and `/login` for staff. The admin is at `/dashboard`.
- Birthday and anniversary reminders run from the scheduler. On the server add one cron line:
  `* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1`
  Reminders only auto-send for branches that turn "auto-send" on in Occasions.

## First things to set up in the admin
1. Cakes & Catalogue: Sizes, Flavours, Collections, then Cakes (with a price per size).
2. Delivery Zones: areas you deliver to and the fee for each.
3. Occasions: reminder wording and how many days ahead to remind.
4. Roles: `super-admin` and `manager` see everything (managers cannot manage branches). `staff` is front desk. `packer` and `delivery` only see orders.

## Still to add when the client provides them
- M-Pesa / payment details (paybill or till) for payment messages and reminders.
- Final business name, phone, address, logo and cake photos.

## Notes
- The old registration and OTP sign-up files (`RegisterController`, `CompleteProfileController`, `RegisterVerificationController`, and the `register` / `verify-*` / `complete-profile` views) are unused and can be deleted.
- After any permission change re-run: `php artisan db:seed --class=RolesAndPermissionsSeeder`

## Branding a new client (no code changes)
Everything client-specific is configurable:
- **In the admin:** Settings > Branding. Business name, tagline, phone, WhatsApp, email, address, logo upload and main colour. These update the website, login page, admin sidebar and top bar, invoice/quotation/statement/receipt PDFs, and the name in SMS messages.
- **In `.env`** (defaults used until the Branding page is saved): `CAKECRAFT_NAME`, `CAKECRAFT_TAGLINE`, `CAKECRAFT_PHONE`, `CAKECRAFT_WHATSAPP`, `CAKECRAFT_EMAIL`, `CAKECRAFT_ADDRESS`, `CAKECRAFT_COLOR` (e.g. `"#b76e79"`, keep the quotes) and `CAKECRAFT_LOGO` (a path inside `/public`, e.g. `images/clientlogo.png`).
- Uploaded logos are stored under `storage/app/public/branding`, so `php artisan storage:link` must have been run.
- Settings > Branding also edits the home page headline, intro, the four "how ordering works" steps and the search-engine description. Section headings further down the home page are still fixed text.
