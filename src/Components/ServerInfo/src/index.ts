import { CardStore } from '@/Card/src/stores'
import { gettext } from '@/Language/src'
import { ServerInfo as component } from './components'
import { ServerInfoStore } from './stores'
ServerInfoStore.enabled &&
  CardStore.addCard({
    id: ServerInfoStore.ID,
    title: gettext('Server Information'),
    tinyTitle: gettext('Info'),
    priority: 300,
    component,
  })
