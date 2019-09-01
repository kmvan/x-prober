import React, { Component } from 'react'
import { observer } from 'mobx-react'
import CardGrid from '~components/Card/src/components/card-grid'
import { gettext } from '~components/Language/src'
import store from '../stores'
import ProgressBar from '~components/ProgressBar/src/components'

@observer
class MemRealUsage extends Component {
  public render() {
    const { max, value } = store.memRealUsage

    return (
      <CardGrid title={gettext('Memory real usage')} tablet={[1, 1]}>
        <ProgressBar value={value} max={max} isCapacity={true} />
      </CardGrid>
    )
  }
}

export default MemRealUsage
