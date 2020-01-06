import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'
import store from './stores'

CardStore.addCard({
  id: store.ID,
  title: gettext('Database'),
  tinyTitle: gettext('DB'),
  priority: 600,
  component,
})
