# Magento2_SomethingDigital_InvalidateAdminPasswords

[![Build Status](https://travis-ci.org/sdinteractive/Magento2_SomethingDigital_InvalidateAdminPasswords.svg?branch=master)](https://travis-ci.org/sdinteractive/Magento2_SomethingDigital_InvalidateAdminPasswords)

A tool to invalidate all Magento admin user passwords

## Why

If you're dealing with a Magento site that has experienced a breach, it's a good idea to reset all admin user passwords.

## Usage

This module adds a new `bin/magento` command, `sd:invalidate-admin-passwords:invalidate`

```
$ php bin/magento sd:invalidate-admin-passwords:invalidate -h
Description:
  Invalidate admin passwords

Usage:
  sd:invalidate-admin-passwords:invalidate

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

As the action is quite destructive (as expected) you'll be asked to confirm before the command runs.

## Configuration

### Settings

Settings are available via the Magento admin panel under Stores > Configuration > Advanced > Admin > Admin User Emails.

- **Send Password Reset Required Email** If set to "Yes" invalidating admin user passwords will also trigger an email to each admin user informing them that their password must be reset
- **Password Reset Required Template** The email template that will be sent to each user.
- **Clear MSP Two Factor Auth** If set to "Yes" invalidating admin user passwords will also invalidate MSP 2FA user configuration. This is recommended as it's possible 2FA configuration may have been compromised (in addition to passwords)

### Customizing The Email Template

The email template can be customized under Marketing > Communications > Email Templates.

1. Click Add New Template
2. From the "Template" dropdown select "Password reset required" under SomethingDigital_InvalidateAdminPasswords
3. Customize as desired

Next head back to Stores > Configuration > Advanced > Admin > Admin User Emails and point "Password Reset Required Template" to your newly customized template.

## Clearing Active Admin Sessions

This module will not kick out admin users with active login sessions. As Magento uses the same storage for customer and admin sessions there is no great way to clear ONLY admin user sessions. If you'd like to kick out users with active login sessions the best option is to wipe all sessions.

### Redis

Issue a `FLUSHDB` command to the Redis instance configured to store session data

### Files

Delete `rm -rf` the session directory (typically `var/session`) 

## How It Works

The module uses the mechanics outlined in [this blog post](https://maxchadwick.xyz/blog/invalidating-all-admin-passwords-in-magento). All passwords are set to a string (`SomethingDigital\InvalidateAdminPasswords\Model\Invalidator::INVALIDATED_PASSWORD_STRING`) that no other string will hash to.
