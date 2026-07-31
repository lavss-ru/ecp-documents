# ECP Documents

WordPress plugin for publishing PDF documents with electronic signature (`.sig`) files.

> 🚧 **Project status:** Under active development.

---

## Features

- Upload PDF documents via the WordPress Media Library.
- Attach electronic signature (`.sig`) files.
- Automatic document title detection.
- Classic Editor integration.
- Shortcode-based document output.
- Clean architecture based on WordPress best practices.
- Composer with PSR-4 autoloading.

---

## Requirements

- WordPress 6.8+
- PHP 8.1+
- Composer (for development)

---

## Installation

Clone the repository:

```bash
git clone https://github.com/lavss-ru/ecp-documents.git
```

Install Composer dependencies:

```bash
composer install
```

Activate the plugin in WordPress.

---

## Development

The project follows:

- WordPress Coding Standards (WPCS)
- PSR-4 autoloading
- Composer
- One logical task per commit

---

## Documentation

Project documentation is available in the `docs/` directory:

- [HANDOFF](docs/HANDOFF.md)
- [ARCHITECTURE](docs/ARCHITECTURE.md)
- [SPECIFICATION](docs/SPECIFICATION.md)
- [DECISIONS](docs/DECISIONS.md)
- [ROADMAP](docs/ROADMAP.md)
- [CONTRIBUTING](docs/CONTRIBUTING.md)
- [AI_CONTEXT](docs/AI_CONTEXT.md)

---

## Project Structure

```text
assets/
docs/
languages/
src/
templates/
vendor/
```

---

## Roadmap

Current priorities:

- Complete Classic Editor integration
- Implement shortcode rendering
- Frontend output
- Version 1.0 release

See the full roadmap in:

- [ROADMAP](docs/ROADMAP.md)

---

## License

This project is licensed under the MIT License.

See the [LICENSE](LICENSE) file for details.
