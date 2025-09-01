=== Custom connect button block for Discord ===
Contributors:      vanbom,webbdeveloper
Donate link:       https://paypal.me/younesdro
Tags:              block, discord, gutenberg, membership, LMS
Tested up to:      6.8
Stable tag:        1.0.1
Requires at least: 6.8
Requires PHP:      7.4
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

A Gutenberg block that provides a customizable "Connect to Discord" button, designed to work with supported Discord integration add-ons.

== Description ==

**Custom Connect Button Block for Discord** adds a flexible Gutenberg block that allows users to connect or disconnect their Discord accounts from any page or post.

The block is fully customizable (text, colors, text, styles) and can display each user’s connection status, including the Discord roles they will be assigned (via the integration add-on).

Unlike shortcodes, this block supports unique design variations per page and offers a real-time preview toggle directly in the editor, so you can instantly view the exact frontend output without publishing.

### Features
– Add a fully customizable "Connect to Discord" button.
– Display a "Disconnect" button for already connected users.
– Show assigned Discord roles dynamically.
– Real-time live preview in the editor with Play/Stop toggle.
– No coding required – all options are visual in the block editor.

### Supported Add-ons
This block requires one of the following plugins for the actual Discord integration (authentication and role sync):
– [PMPro Discord Add-on](https://wordpress.org/plugins/pmpro-discord-add-on/)
– [ExpressTech MemberPress Discord Add-on](https://wordpress.org/plugins/expresstechsoftwares-memberpress-discord-add-on/)
– [Connect Ultimate Member to Discord](https://wordpress.org/plugins/ultimate-member-discord-add-on/)
– [Connect Tutor LMS to Discord](https://wordpress.org/plugins/connect-tutorlms-to-discord/)

This plugin’s source code is [available on GitHub](https://github.com/younes-dro/custom-connect-button-block-for-discord).
Contributions and bug reports are welcome.

== External services ==

This plugin does not connect to external APIs directly.
Instead, supported add-ons (listed above) handle all communication with the **Discord API**.

**Optional external call used by this plugin:**
– **Discord CDN (https://cdn.discordapp.com/)** — to display a connected user’s avatar image, if provided by the add-on.
– **Data sent:** None.
– **Data retrieved:** Avatar image.
– **When:** Only when the user is already connected to Discord through a supported add-on.

For more details:
– [Discord Terms of Service](https://discord.com/terms)
– [Discord Privacy Policy](https://discord.com/privacy)

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/custom-connect-button-block-for-discord/` or install directly from the WordPress Plugins screen.
2. Activate the plugin through the "Plugins" menu in WordPress.

== Frequently Asked Questions ==

= What does this block do? =
It provides a customizable "Connect to Discord" button that works alongside supported add-ons. Those add-ons handle the actual Discord connection, while this block lets you design and place the button anywhere in the editor.

= Does this block work with any Discord integration plugin? =
It currently supports the PMPro, MemberPress, Ultimate Member, and Tutor LMS Discord add-ons.

= Can I customize the button style? =
Yes. You can set the button’s text, colors, and text directly in the Gutenberg editor.

= Can I preview changes live before saving? =
Yes. The block includes a Play/Stop toggle that renders the live frontend output instantly inside the editor.

== Screenshots ==
1. Initial block status - buttons + text
2. Enable live preview for non-connected user
3. Live preview for connected user to Discord
4. The button text placeholder settings
5. The color settings for buttons: front color and background color

== Changelog ==

= 1.0.0 =
* Initial release.
