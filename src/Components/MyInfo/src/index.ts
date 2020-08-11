import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'
import store from './stores'

store.enabled &&
  CardStore.addCard({
    id: store.ID,
    title: gettext('My Information'),
    tinyTitle: gettext('Mine'),
    priority: 900,
    component,
  })
