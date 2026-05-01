## OVERVIEW

Core adaptive engine logic components. Contains Constants for Facts, Actions, and Pedagogy.

---

## WHERE TO LOOK

- **Constants**: `app/Rules/Adaptive/Constants/` holds enums/constants for facts (`FactConstants.php`), actions (`ActionConstants.php`), and pedagogical thresholds (`PedagogicalConstants.php`).
- **Student State Schema**: `StudentStateSchema.php` defines the structure of the JSON state.
- **Rule Engine**: The actual forward-chaining rule evaluation logic lives in `app/Services/Adaptive/AdaptiveEngineService.php`.
- **Database Models**: The rules themselves are stored in the database and represented by `AdaptiveRule`, `AdaptiveFact`, and `AdaptiveAction` models.

---

## CONVENTIONS

- **Constants**: Use constants from this directory when referencing facts (e.g., `G01`, `G02`) or actions (e.g., `H01`, `H05`) in code.
- **No Hardcoding**: Do not hardcode "G01" or "H01" strings in services. Always use the constants.
- **Separation**: This directory is strictly for Rule components and constants. The orchestration lives in `Services`.

---

## ANTI-PATTERNS

- Creating file-based rule classes. Rules are configured in the database, not hardcoded as PHP classes.
- Putting business logic or service execution inside this folder.
- Hardcoding fact or action codes anywhere in the application.
