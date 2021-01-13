import CardStore from '@/Card/src/stores'
import component from './components'
import store from './stores'
import { gettext } from '@/Language/src'
store.enabled &&
  CardStore.addCard({
    id: store.ID,
    title: gettext('PHP Information'),
    tinyTitle: gettext('PHP'),
    priority: 400,
    component,
  })
