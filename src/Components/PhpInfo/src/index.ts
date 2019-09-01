import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'

CardStore.addCard({
  id: 'phpInfo',
  title: gettext('PHP Information'),
  tinyTitle: gettext('PHP'),
  priority: 400,
  component,
})
