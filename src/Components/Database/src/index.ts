import { CardStore } from '@/Card/src/stores'
import { gettext } from '@/Language/src'
import { Database as component } from './components'
import { DatabaseStore } from './stores'
DatabaseStore.enabled &&
  CardStore.addCard({
    id: DatabaseStore.ID,
    title: gettext('Database'),
    tinyTitle: gettext('DB'),
    priority: 600,
    component,
  })
