<h1 align="center">ramazancetinkaya/session-handler</h1>

<p align="center">
    <strong>A high-quality, powerful, and secure session handler class for PHP.</strong>
</p>

###

<p align="center">
    :star: Star us on GitHub — it motivates us a lot!
</p>

## Session Handler
A session handler is a custom PHP class that can be used to handle the storage and retrieval of session data. This allows developers to have greater control over how session data is stored, including the ability to store it in alternative storage systems like databases, rather than the default file system.

## Benefits of using a custom session handler
- More control over session data storage.
- Ability to store session data in alternative storage systems like databases.
- Improved security and performance compared to the default file system storage.
- The ability to manage session data more efficiently and effectively.

## Conclusion
By using a custom session handler, you can have greater control over the storage and retrieval of session data in your PHP applications. Whether you're looking to improve security, performance, or just have more control over your session data, a custom session handler can be a powerful tool for your PHP development toolkit.

## Features
- Uses the AES-256-CBC encryption algorithm for maximum security.
- Adheres to coding standards.
- Comprehensive functionality.
- Garbage collector to automatically delete expired sessions.

## Usage

```php
session_set_save_handler(new SessionHandler('/path/to/save/sessions', 'secret-key'), true);
session_start();

// Now you can use the $_SESSION superglobal as you normally would.
$_SESSION['user_id'] = 42;

// Don't forget to close the session.
session_write_close();
```

In this example, the SessionHandler class is loaded and session_set_save_handler function is used to allow it to manage the session processing. Then the session is started and the $_SESSION superglobal can be used as normal. Finally, the session must be closed.

## Installation

The preferred method of installation is via [Composer](https://getcomposer.org/).

Run the following command to install the package:
```bash
composer require ramazancetinkaya/session-handler
```

## Contributing
If you would like to contribute to the development of this class, I would be more than happy to consider your contributions!

###

To contribute, simply fork the repository, make your changes, and submit a pull request back to the main repository. I will review your changes and, if they meet the project's standards and requirements, I will merge them into the main codebase.

###

Before submitting your changes, please make sure to test your code thoroughly and ensure that it is properly documented. Also, please make sure to follow the project's coding standards and conventions.

###

Thank you for your interest in contributing to this project!

## Code of Conduct
We are committed to creating a friendly and inclusive environment for all contributors and users of this repository. To help achieve this goal, we have adopted a Code of Conduct that outlines our expectations for behavior as well as the consequences for unacceptable behavior.

###

We strongly encourage all contributors and users to read and follow the Code of Conduct, which can be found in the CODE_OF_CONDUCT.md file. By participating in this community, you agree to abide by its rules and guidelines.

###

If you encounter a situation that you feel is not aligned with our Code of Conduct, please report it by contacting the repository owner. We will work to address the situation promptly and respectfully.

###

Together, let's create a positive and welcoming community!

## Disclaimer

The software and its copyright are owned solely by the author. Any commercial use of the software requires the author's permission. The author and the copyright holders are not responsible for any errors or problems that may have been overlooked in contributions, reviews, or any other form of modification to the software.

The author and the copyright holders shall not be held responsible for any damages arising from the use of this software. The software is provided "as is", without warranty of any kind, express or implied.

In no event shall the author or the copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other dealings in the software.

This project is intended for educational and informational purposes only, and should not be considered as professional advice or recommendations. The author encourages you to seek appropriate professional guidance before taking any action based on the information and contents provided in this project.

###

By using this software, you agree to the above terms and conditions. If you do not agree with these terms, you should not use this software.

## Support
If you encounter any issues while using this class, or if you have any questions or feedback, please feel free to open an issue on the repository's issue tracker. I will do my best to assist you as soon as possible.

###

Note that while I am happy to help and provide support, I cannot guarantee immediate resolution of all issues. If you need more immediate support, you may want to consider reaching out to a professional services provider for assistance.

## Authors

Ramazan Çetinkaya

- [github/ramazancetinkaya](https://github.com/ramazancetinkaya)

## Copyright and License

Copyright © 2023, [Ramazan Çetinkaya](https://github.com/ramazancetinkaya).
