# Documentarian

#### Simply write beautiful API documentation.

This project is a fork of the original [Documentarian](https://github.com/mpociot/documentarian) PHP porting of
[Slate](https://github.com/tripit/slate) API documentation tool. This fork will allow you to build
API documentation for multiple API versions.

> If PHP is not your thing and you're more into nodeJS, why not give [Whiteboard](https://github.com/mpociot/whiteboard) a try?

<a href="http://marcelpociot.de/whiteboard/"><img src="http://marcelpociot.de/git/whiteboard_responsive.jpg" style="width: 100%" alt="Documentarian" /></a>

### Installation

To install Documentarian globally run

```bash
    $ composer global require elevenlab/documentarian
```

### Create documentation

To create a documentation project run

```bash
    $ documentarian create <folder>
```

if `folder` is not specified the documentation will be created in the current working directory.

#### Example:

```bash
    $ documentarian create kittens-api
```

### Create docs for specific api version

To create an api-specific documentation run the following command

```bash
    $ documentarian create_version <documentation-folder> <version-name>
```

A folder named `<version-name>` will be created in `<documentation-folder>/source/<version-name>`. Edit the `index.md`
file in this folder to customize your api-specific documentation.

#### Example

```bash
    $ documentarian create_version kittens-api v1
    $ documentarian create_version kittens-api v2
```

### Generate documentation pages

To generate `.html` files of the api-specific documentation run the command

```bash
    $ documentarian generate <folder>
```

where `<folder>` is your documentation parent folder. This will create a `public` folder inside your `<folder>` that
contains both frontend assets (like css and javascript) and the generated `.html` pages of your api documentation versions.

#### Example

```bash
    $ documentarian generate kittens-api
```

The above command will generate a `public` folder inside `kittens-api` that consists of the following content:

- `css` folder - contains style assets
- `images` folder - contains images used in the documentation
- `js` folder - contains JavaScript scripts used by the documentation page
- `v1.html` file - Documentation page for api version `v1`
- `v2.html` file - Documentation page for api version `v2`

### Changing template structure

If you wish to change the documentation file template, edit the file `<documentation-foler>/views/index.blade.php`
as you like.

### Setting version links

To setup versions links that points to the right api version documentation page, edit the section
`version` in `index.md`:

```yaml
versions:
  v1:
    link: v1.html
    target: _blank
  v2:
    name: latest
    link: v2.html
```

For each version you can set the following parameters:

 - `link` - Link of the documentation page (href)
 - `target` - Target of `href` link (see [HTML target attribute](https://www.w3schools.com/tags/att_a_target.asp))
 - `name`   - Custom version name to be displayed on the rendered page, leave blank if you wish to use plain version name
 
### In depth documentation

For further documentation on how to customize theme and other aspects, read the official whiteboard [documentation](http://marcelpociot.de/documentarian/installation).

### Slate / Whiteboard compatibility
Since both Documentarian and Slate use regular markdown files to render the API documentation, your existing Slate API documentation should work just fine. If you encounter any issues, please [submit an issue](https://github.com/mpociot/documentarian/issues).

### Contributors

Slate was built by [Robert Lord](https://lord.io) while at [TripIt](http://tripit.com).

Documentarian was built by Marcel Pociot.

Multi-version Documentarian was built by Valerio Cervo @ [Eleven](http://moveax.it)
