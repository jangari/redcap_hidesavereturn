# HIDESAVERETURN

This REDCap External Module allows users to conditionally hide the Save & Return Later button on surveys using Action Tags and branching logic.

This action tag is used on descriptive text fields whose visibility on the survey will prevent the visibility of the Save & Return Later button.

## Installation

Install the module from the REDCap module repository and enable in the Control Center, then enable on projects.

## Usage

This module adds one action tag:

- @HIDESAVERETURN – Hides the Save and Return Later button on surveys _if the field is visible due to branching logic_.

## Acknowledgements

This external module is basically a clone of the [HIDESUBMIT Action Tags](https://github.com/jangari/redcap_hidesubmit) external module, targeting the Save & Return Later button instead. It was developed after a suggestion from [@lucheng.kuo](https://redcap.vanderbilt.edu/community/profile.php?id=2725) and [@lara.lechtenberg2](https://redcap.vanderbilt.edu/community/profile.php?id=4382) on the REDCap Community.

## Citation

If you use this external module for a project that generates a research output, please cite this software in addition to [citing REDCap](https://projectredcap.org/resources/citations/) itself. You can do so using the APA referencing style as below:

> Wilson, A. (2023). HIDESAVERETURN [Computer software]. https://github.com/jangari/redcap_hidesavereturn 

Or by adding this reference to your BibTeX database:

```bibtex
@software{Wilson_HIDESAVERETURN_2023,
author = {Wilson, Aidan},
title = {{HIDESAVERETURN Action Tags}},
url = {https://github.com/jangari/redcap_hidesavereturn},
year = {2023}
month = {3},
}
```

These instructions are also available in [GitHub]( https://github.com/jangari/redcap_hidesavereturn) under 'Cite This Repository'.

## Changelog

| Version | Description                                                                                           |
| ------- | --------------------                                                                                  |
| v1.0.0  | Initial release.                                                                                      |
| v1.1.0  | Migrated to EM Framework built-in support for Action Tag help dialgue  |
