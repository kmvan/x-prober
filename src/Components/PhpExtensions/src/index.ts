import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'
import store from './stores'

CardStore.addCard({
  id: store.ID,
  title: gettext('PHP extensions'),
  tinyTitle: gettext('Ext'),
  priority: 500,
  component,
})
