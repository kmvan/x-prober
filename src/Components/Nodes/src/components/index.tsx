import React, { Component } from 'react'
import { observer } from 'mobx-react'
import Row from '@/Grid/src/components/row'
import store from '../stores'
import Grid from '@/Grid/src/components/grid'
import styled from 'styled-components'
import { OK } from '@/Restful/src/http-status'
import { gettext } from '@/Language/src'
import { SysLoadGroup } from '@/ServerStatus/src/components/system-load'
import ProgressBar from '@/ProgressBar/src/components'
import template from '@/Helper/src/components/template'
import {
  ServerStatusCpuUsageProps,
  ServerStatusUsageProps,
} from '@/ServerStatus/src/stores'
import { GUTTER } from '@/Config/src'
import Alert from '@/Helper/src/components/alert'
import Loading from '@/Helper/src/components/loading'
import restfulFetch from '@/Fetch/src/restful-fetch'
import NodeNetworks from './node-networks'

const StyledNodeGroupId = styled.a`
  display: block;
  text-decoration: underline;
  text-align: center;
  margin-bottom: calc(${GUTTER} / 2);
  :hover {
    text-decoration: none;
  }
`
const StyledNodeGroup = styled.div`
  margin-bottom: calc(${GUTTER} / 2);
`
const StyledNodeGroupMsg = styled(StyledNodeGroup)`
  display: flex;
  justify-content: center;
`

@observer
export default class Nodes extends Component {
  public componentDidMount() {
    const { items, itemsCount } = store

    if (!itemsCount) {
      return
    }

    for (const { id } of items) {
      this.fetch(id)
    }
  }

  private fetch = async (nodeId: string) => {
    const { setItem } = store

    await restfulFetch(`node&nodeId=${nodeId}`)
      .then(([{ status }, item]) => {
        if (status === OK) {
          if (!item) {
            return
          }

          setItem({ id: nodeId, isLoading: false, data: item })
          // fetch again
          setTimeout(() => {
            this.fetch(nodeId)
          }, 1000)
        } else {
          setItem({
            id: nodeId,
            isLoading: false,
            isError: true,
            errMsg: template(gettext('Fetch failed. Node returns ${code}.'), {
              code: status,
            }),
          })
        }
      })
      .catch(e => {
        setItem({
          id: nodeId,
          isLoading: false,
          isError: true,
          errMsg: gettext('Fetch failed. Detail in Console.'),
        })
        console.log(
          template(gettext('Node [${nodeId}] fetch failed.'), { nodeId }),
          e
        )
      })
  }

  private renderSysLoad(sysLoad: number[]) {
    if (!sysLoad?.length) {
      return null
    }

    return (
      <StyledNodeGroup>
        <SysLoadGroup isCenter sysLoad={sysLoad} />
      </StyledNodeGroup>
    )
  }

  private renderCpu(cpuUsage: ServerStatusCpuUsageProps) {
    return (
      <StyledNodeGroup>
        <ProgressBar
          title={template(
            gettext(
              'idle: ${idle} \nnice: ${nice} \nsys: ${sys} \nuser: ${user}'
            ),
            cpuUsage
          )}
          value={100 - cpuUsage.idle}
          max={100}
          isCapacity={false}
          left={gettext('CPU usage')}
        />
      </StyledNodeGroup>
    )
  }

  private renderMemory({ value, max }: ServerStatusUsageProps) {
    const percent = Math.floor((value / max) * 10000) / 100

    return (
      <StyledNodeGroup>
        <ProgressBar
          title={template(gettext('Usage: ${percent}'), {
            percent: `${percent.toFixed(1)}%`,
          })}
          value={value}
          max={max}
          isCapacity
          left={gettext('Memory')}
        />
      </StyledNodeGroup>
    )
  }

  private renderSwap({ value, max }: ServerStatusUsageProps) {
    const percent = Math.floor((value / max) * 10000) / 100

    return (
      <StyledNodeGroup>
        <ProgressBar
          title={template(gettext('Usage: ${percent}'), {
            percent: `${percent.toFixed(1)}%`,
          })}
          value={value}
          max={max}
          isCapacity
          left={gettext('Swap')}
        />
      </StyledNodeGroup>
    )
  }

  private renderItems() {
    return store.items.map(({ id, url, isLoading, isError, errMsg, data }) => {
      const idLink = <StyledNodeGroupId href={url}>{id}</StyledNodeGroupId>

      switch (true) {
        case isLoading:
          return (
            <Grid key={id} tablet={[1, 4]} mobileLg={[1, 2]}>
              {idLink}
              <StyledNodeGroupMsg>
                <Loading>{gettext('Fetching...')}</Loading>
              </StyledNodeGroupMsg>
            </Grid>
          )
        case isError:
          return (
            <Grid key={id} tablet={[1, 4]} mobileLg={[1, 2]}>
              {idLink}
              <StyledNodeGroupMsg>
                <Alert isSuccess={false} msg={errMsg} />
              </StyledNodeGroupMsg>
            </Grid>
          )
      }

      const { serverStatus, networkStats } = data

      return (
        <Grid
          key={id}
          tablet={[1, 2]}
          desktopSm={[1, 3]}
          desktopMd={[1, 4]}
          desktopLg={[1, 6]}
        >
          {idLink}
          {this.renderSysLoad(serverStatus.sysLoad)}
          {this.renderCpu(serverStatus?.cpuUsage)}
          {this.renderMemory(serverStatus?.memRealUsage)}
          {this.renderSwap(serverStatus?.swapUsage)}
          <NodeNetworks
            items={networkStats?.networks || []}
            timestamp={networkStats?.timestamp || 0}
          />
        </Grid>
      )
    })
  }

  public render() {
    return <Row>{this.renderItems()}</Row>
  }
}
