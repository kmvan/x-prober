import { CardStore } from '../Card/stores'
import { gettext } from '../Language'
import { PhpExtensions as component } from './components'
import { PhpExtensionsConstants } from './constants'
export const PhpExtensionsBootstrap = (): void => {
  const { id, isEnable } = PhpExtensionsConstants
  isEnable &&
    CardStore.addCard({
      id,
      title: gettext('PHP Extensions'),
      tinyTitle: gettext('Ext'),
      priority: 500,
      component,
    })
}
