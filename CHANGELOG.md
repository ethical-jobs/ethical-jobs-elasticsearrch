# v1.5.2

- Removed logging for single document indexing, too noisy.

# v1.5.1

- Swallowing and logging indexing obervables

# v1.5.0

- Reducing slack logging calls
- Queing at the indexable level, not a user defined process level
- Small logging changes

# v1.4.8

- Indexing command lock

# v1.4.7

- Bug fixes
- Test suite refactor and extension

# v1.4.1

- Adding elasticsearch repositories
- Repository tests

# v1.3.10

- Async query indexing fixes and refactors
- Serializable IndexQueries

# v1.3.0

- Added async indexing feature
- Added slack indexing logger

# v1.2.2

- Removing default settings and mappings from config
- Reduced indexing chunk-size to 300

# v1.2.0

- Updated observers to not fire additional non-needed events / listeners
- Extended testing aaround observers and indexing

# v1.0.0

- Initial version