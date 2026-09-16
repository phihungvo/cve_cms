export ANTHROPIC_API_KEY=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



val "$(/opt/homebrew/bin/brew shellenv)"


# Herd injected PHP 8.4 configuration.
export HERD_PHP_84_INI_SCAN_DIR="/Users/phihung/Library/Application Support/Herd/config/php/84/"


# Herd injected NVM configuration
export NVM_DIR="/Users/phihung/Library/Application Support/Herd/config/nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm

[[ -f "/Applications/Herd.app/Contents/Resources/config/shell/zshrc.zsh" ]] && builtin source "/Applications/Herd.app/Contents/Resources/config/shell/zshrc.zsh"

# Herd injected PHP binary.
export PATH="/Users/phihung/Library/Application Support/Herd/bin/":$PATH


# Herd injected PHP 8.2 configuration.
export HERD_PHP_82_INI_SCAN_DIR="/Users/phihung/Library/Application Support/Herd/config/php/82/"


# Herd injected PHP 8.3 configuration.
export HERD_PHP_83_INI_SCAN_DIR="/Users/phihung/Library/Application Support/Herd/config/php/83/"
export PATH="/opt/homebrew/opt/mysql-client/bin:$PATH"

#THIS MUST BE AT THE END OF THE FILE FOR SDKMAN TO WORK!!!
export SDKMAN_DIR="$HOME/.sdkman"
[[ -s "$HOME/.sdkman/bin/sdkman-init.sh" ]] && source "$HOME/.sdkman/bin/sdkman-init.sh"
export PATH="/Applications/Docker.app/Contents/Resources/bin:$PATH"


# Herd injected PHP 7.4 configuration.
export HERD_PHP_74_INI_SCAN_DIR="/Users/phihung/Library/Application Support/Herd/config/php/74/"
