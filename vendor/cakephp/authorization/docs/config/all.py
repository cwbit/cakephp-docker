# Global configuration information used across all the
# translations of documentation.
#
# Import the base theme configuration
from cakephpsphinx.config.all import *

# The version info for the project you're documenting, acts as replacement for
# |version| and |release|, also used in various other places throughout the
# built documents.
#

# The full version, including alpha/beta/rc tags.
release = '2.x'

# The search index version
search_version = 'authorization-2'

# The marketing display name for the book.
version_name = ''

# Project name shown in the black header bar
project = 'CakePHP Authorization'

# Other versions that display in the version picker menu.
version_list = [
    {'name': '1.x', 'number': '/authorization/1.1', 'title': '1.x'},
    {'name': '2.x', 'number': '/authorization/2.x', 'title': '2.x', 'current': True},
]

# Languages available.
languages = ['en', 'es', 'fr']

# The GitHub branch name for this version of the docs
# for edit links to point at.
branch = '2.x'

# Current version being built
version = '2.x'

show_root_link = True

repository = 'cakephp/authorization'

source_path = 'docs/'

hide_page_contents = ('search', '404', 'contents')
