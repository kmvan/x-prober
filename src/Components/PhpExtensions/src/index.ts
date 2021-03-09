import { CardStore } from '@/Card/src/stores'
import { gettext } from '@/Language/src'
import { PhpExtensions as component } from './components'
import { PhpExtensionsStore } from './stores'
PhpExtensionsStore.enabled &&
  CardStore.addCard({
    id: PhpExtensionsStore.ID,
    title: gettext('PHP Extensions'),
    tinyTitle: gettext('Ext'),
    priority: 500,
    component,
  })
