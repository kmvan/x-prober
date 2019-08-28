import React, { Component } from 'react'
import { observer } from 'mobx-react'
import CardGrid from '~components/Card/src/components/card-grid'
import { gettext } from '~components/Language/src'
import store from '../stores'
import ProgressBar from '~components/ProgressBar/src/components'

@observer
class CpuUsage extends Component {
  public render() {
    return (
      <CardGrid title={gettext('CPU usage')} tablet={[1, 1]}>
        <ProgressBar value={store.cpuUsage} max={100} isCapacity={false} />
      </CardGrid>
    )
  }
}

export default CpuUsage
