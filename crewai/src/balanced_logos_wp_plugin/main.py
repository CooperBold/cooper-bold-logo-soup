#!/usr/bin/env python3
"""Run the Logo Soup WP Plugin planning crew."""

import sys

from logo_soup_wp_plugin.crew import LogoSoupWpPluginCrew


def main():
    if len(sys.argv) > 1:
        task_description = " ".join(sys.argv[1:])
    else:
        task_description = (
            sys.stdin.read().strip()
            or "Scaffold the Logo Soup WordPress plugin with admin UI and shortcode."
        )
    inputs = {"task_description": task_description}
    LogoSoupWpPluginCrew().crew().kickoff(inputs=inputs)


if __name__ == "__main__":
    main()
