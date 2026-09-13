# Display On Date — Dashboard Module

A lightweight **GetSimple CMS CE Dashboard module** that displays scheduled blocks from the **Display On Date** plugin directly on the GetSimple dashboard.

The module provides a quick overview of scheduled blocks, including their start and end dates, current status, and a shortcut to edit each block.

<img width="734" alt="DisplayOnDate-module" src="https://github.com/user-attachments/assets/200da4cf-af17-4ca8-96c5-55bd404e35cc" />

## Features

- Displays scheduled Display On Date blocks on the GetSimple dashboard.
- Shows:
  - Block key
  - Start date
  - End date
  - Current status
  - Edit action
- Automatically categorises blocks as:
  - **Active**
  - **Upcoming**
  - **Expired**
- Active blocks are shown first, followed by upcoming and expired blocks.
- Provides a **New Block** shortcut.
- Scrollable block list for dashboards containing many scheduled blocks.
- Uses GetSimple CMS CE's dashboard module system.
- Includes English and Spanish translations.
- Does not require JavaScript or external libraries.
- Styling is scoped to the module to avoid affecting other dashboard modules.

## Requirement

- GetSimpleCMS-CE with the Dashboard plugin installed and active

- This module requires the **Display On Date** plugin to be installed and active.

If the Display On Date plugin is not active, the module displays a warning instead of attempting to load scheduled blocks.

## Installation

1. Copy `customlinks.php` into `plugins/Dashboard/modules/`
2. Copy the `customlinks/` folder (containing `lang/en_US.php`, `lang/es_ES.php`, `lang/.htaccess`) into `plugins/Dashboard/modules/`

Your folder structure should look like this:

```
plugins/
└── Dashboard/
    └── modules/
        ├── displayondate.php
        └── displayondate/
            └── lang/
                ├── en_US.php
                ├── es_ES.php
                └── .htaccess
```

3. In GetSimple, go to **Dashboard → Layout** (gear icon on the Dashboard page) and enable the **Display On Date** module
4. Save the layout

## Dependency

The module detects whether the Display On Date plugin is available by checking for its public function:

```php
displayon_get_blocks()
```

When the plugin is active, the module retrieves the scheduled blocks using:

```php
$blocks = displayon_get_blocks();
```

If the function is unavailable, the module displays:

> Display On Date plugin is not active.

## Dashboard Display

Each scheduled block is displayed in a compact table:

| Column | Description |
|---|---|
| Key | The identifier/key of the scheduled block |
| Start | The block's scheduled start date |
| End | The block's scheduled end date |
| Status | The current status of the block |
| Action | Link to edit the block |

### Status

The status is calculated automatically using the current server time.

**Active**

The current time is between the block's start and end dates.

**Upcoming**

The block's start date has not yet been reached.

**Expired**

The block's end date has passed.

Blocks are sorted with active blocks first, followed by upcoming blocks and then expired blocks. Within each group, blocks are ordered by their end date.

## Actions

### New Block

The **+ New Block** button opens the Display On Date plugin's new-block screen.

### Edit

Each block has an **Edit** button which opens the corresponding block in the Display On Date plugin.

## Empty State

If the Display On Date plugin is active but there are no scheduled blocks, the module displays:

> No scheduled blocks yet.

## Languages

The module currently includes:

- English (`en_US`)
- Spanish (`es_ES`)

Additional translations can be added to:

```text
displayondate/lang/
```

The module uses the Dashboard module internationalisation function:

```php
dash_module_i18n('displayondate');
```

## Compatibility

Designed for:

- GetSimple CMS CE
- GetSimple CMS CE Dashboard Modules
- Display On Date plugin

The module is intentionally lightweight and uses PHP, HTML and CSS only.
