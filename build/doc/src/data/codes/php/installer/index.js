export const composer = `
{
  "extra": {
    "installer-paths": {
      "local/modules/{$name}/": ["type:bitrix-module"]
    }
  },
  "require": {
    "sholokhov/bitrix-exchange": "dev-master"
  },
}
`