# Custom Connect Button Block for Discord

## 🧩 Features

- Gutenberg block for the WordPress block editor
- Full styling control:
  - Button text customization
  - Button background and text color (connect/disconnect states)
- Dynamic messaging based on connection state
- Role assignment messaging
- Connected account display
- Conditional visibility based on login status
- Seamless integration with popular plugins:
  - **Membership:** Paid Memberships Pro, MemberPress, Ultimate Member, Tutor LMS
- Developer-friendly: Easily extensible with hooks and filters

---

## 🚀 Installation

1. Upload the plugin files to the `/wp-content/plugins/'custom-connect-button-block-for-discord'/` directory, or install it directly through the WordPress admin dashboard.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Edit a post or page using the Gutenberg Block Editor.
4. Search for **"Connect Button for Discord"** block and insert it.
5. Use the block settings panel to customize appearance, behavior, and messages.

---

## ⚙️ Block Settings

Each block instance allows you to customize:

- **Button Appearance**

  - `connectButtonTextColor`: Color of the “Connect” button text
  - `connectButtonBgColor`: Background color for the “Connect” button
  - `disconnectButtonTextColor`: Color of the “Disconnect” button text
  - `disconnectButtonBgColor`: Background color for the “Disconnect” button

- **Text & Messaging**

  - `loggedInText`: Text shown when user is logged in but not connected
  - `loggedOutText`: Text shown when user is connected to Discord
  - `roleWillAssignText`: Message before assigning roles
  - `roleAssignedText`: Message after assigning roles
  - `discordConnectedAccountText`: Label for the connected Discord account

- **Visibility Options**
  - Conditional display based on login status

---

## 🔌 Compatibility

This plugin supports out-of-the-box integrations with:

- **Membership Plugins:**

  - Paid Memberships Pro
  - MemberPress
  - Ultimate Member
  - Tutor LMS

---
