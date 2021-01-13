import CardStore from '@/Card/src/stores'
import component from './components'
import store from './stores'
import { gettext } from '@/Language/src'
store.enabled &&
  CardStore.addCard({
    id: store.ID,
    title: gettext('Server Status'),
    tinyTitle: gettext('Status'),
    priority: 100,
    component,
  })
