=== Certify ===
Contributors: amritkumarchanchal, segwitz
Certify is a simple and powerful WordPress plugin for educational institutions, training centers, and organizations to manage and verify course certificates online.

Tags: Wordpress certificate Verification, education, certify
Requires at least: 4.0
Tested up to: 6.8.1
Stable tag: trunk
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html


== Description ==

Certify allows administrators to add, edit, and bulk upload course certificates from the WordPress admin panel. Students and users can verify their certificates on your website using a simple search form.

**Features:**
- Add, edit, and delete certificates from the admin panel
- Bulk upload certificates via CSV file
- Search and verify certificates on the front end using a shortcode
- Clean and user-friendly interface

== Installation ==

1. Download the Certify plugin ZIP file.
2. In your WordPress admin, go to Plugins → Add New → Upload Plugin and upload the ZIP file.
3. Activate the plugin.
4. Go to the "Certify" menu in your WordPress admin panel to add or bulk upload certificates.
5. To display the certificate verification form on any page, use the shortcode: `[get_certificate_search_form]`

== Usage ==

- **Adding Certificates:**
  - Use the "Add New Certificate" button in the Certify admin menu.
- **Bulk Upload:**
  - Use the "Bulk Upload" button and upload a CSV file with columns: first name, course, hours, certificate no, date of completion.
- **Front-End Verification:**
  - Place the `[get_certificate_search_form]` shortcode on any page or post. Users can enter their certificate number to verify details.

== Credits ==

Originally developed by SegWitz. Now maintained and enhanced by Amrit Kumar Chanchal.

== Screenshots ==

1. Admin panel for managing certificates
2. Bulk upload interface
3. Front-end certificate verification form

== Changelog ==

= 2.0 =
* Major update and rebranding to Certify by Amrit Kumar Chanchal
* Improved UI and bulk upload
* Enhanced security and compatibility

== Support ==

For support or suggestions, contact amritkumarchanchal@gmail.com or open an issue on the plugin repository.
