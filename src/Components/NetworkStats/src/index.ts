import CardStore from '@/Card/src/stores'
import { gettext } from '@/Language/src'
import component from './components'
import store from './stores'

store.enabled &&
  CardStore.addCard({
    id: store.ID,
    title: gettext('Network Stats'),
    tinyTitle: gettext('Net'),
    priority: 200,
    component,
  })
