import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'
import store from './stores'

store.conf &&
  CardStore.addCard({
    id: 'networkStats',
    title: gettext('Network stats'),
    tinyTitle: gettext('Net'),
    priority: 200,
    component,
  })
