import { CardStore } from '@/Card/src/stores'
import { gettext } from '@/Language/src'
import { ServerStatus as component } from './components'
import { ServerStatusStore } from './stores'
ServerStatusStore.enabled &&
  CardStore.addCard({
    id: ServerStatusStore.ID,
    title: gettext('Server Status'),
    tinyTitle: gettext('Status'),
    priority: 100,
    component,
  })
