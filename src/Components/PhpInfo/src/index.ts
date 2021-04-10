import { CardStore } from '@/Card/src/stores'
import { gettext } from '@/Language/src'
import { PhpInfo as component } from './components'
import { PhpInfoStore } from './stores'
PhpInfoStore.enabled &&
  CardStore.addCard({
    id: PhpInfoStore.ID,
    title: gettext('PHP Information'),
    tinyTitle: gettext('PHP'),
    priority: 400,
    component,
  })
