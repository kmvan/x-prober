import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'
import store from './stores'

store.enabled &&
  store.itemsCount &&
  CardStore.addCard({
    id: store.ID,
    title: gettext('Nodes'),
    tinyTitle: gettext('Nodes'),
    priority: 50,
    component,
  })
