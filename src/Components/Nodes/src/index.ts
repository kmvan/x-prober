import { CardStore } from '@/Card/src/stores'
import { gettext } from '@/Language/src'
import { Nodes as component } from './components'
import { NodesStore } from './stores'
NodesStore.enabled &&
  NodesStore.itemsCount &&
  CardStore.addCard({
    id: NodesStore.ID,
    title: gettext('Nodes'),
    tinyTitle: gettext('Nodes'),
    priority: 50,
    component,
  })
