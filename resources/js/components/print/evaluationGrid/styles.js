const styles = {
  page: {
    padding: '18mm',
    fontFamily: 'SourceSansPro',
    fontSize: '11pt',
    lineHeight: '1.6',
  },
  pageNumbering: {
    position: 'absolute',
    bottom: '12mm',
    right: '18mm',
    textAlign: 'right',
  },
  h2: {
    fontSize: '17pt',
  },
  h3: {
    fontSize: '15pt',
    marginBottom: '4pt',
  },
  h5: {
    fontSize: '13pt',
    marginBottom: '3pt',
  },
  h6: {
    fontWeight: 'bold',
    marginBottom: '3pt',
  },
  p: {
    marginBottom: '2.2mm',
  },
  emptyLine: {
    width: '100%',
    height: '16pt',
    borderBottom: '0.5pt solid black',
  },
  evaluationGridRow: {
    borderLeft: '0.5pt solid black',
    borderRight: '0.5pt solid black',
    borderBottom: '0.5pt solid black',
    display: 'flex',
    flexDirection: 'row',
  },
  evaluationGridRowCriterion: {
    flexGrow: '0',
    width: '30%',
    borderRight: '0.5pt solid black',
    padding: '1mm 1.5mm',
  },
  evaluationGridRowValue: {
    flexGrow: '0',
    width: '25%',
    borderRight: '0.5pt solid black',
    padding: '1mm 1.5mm',
  },
  evaluationGridRowNotes: {
    flexGrow: '0',
    width: '45%',
    padding: '1mm 1.5mm',
    minHeight: '35mm',
  },
  evaluationGridRowScale: {
    width: '100%',
    display: 'flex',
    flexDirection: 'column',
    paddingTop: '9mm',
  },
  evaluationGridRowScaleAxisSquish: {
    width: '100%',
    padding: '0 2mm',
    display: 'flex',
    flexDirection: 'column',
  },
  evaluationGridRowScaleTicks: {
    width: '100%',
    display: 'flex',
    flexDirection: 'row',
    borderLeft: '1pt solid black',
  },
  evaluationGridRowScaleTick: {
    height: '2mm',
    borderRight: '1pt solid black',
    flexGrow: '1',
  },
  evaluationGridRowScaleAxis: {
    width: '100%',
    display: 'flex',
    flexDirection: 'column',
    borderTop: '1px solid black',
  },
  evaluationGridRowScaleLabels: {
    width: '100%',
    display: 'flex',
    flexDirection: 'row',
    justifyContent: 'space-between',
    textAlign: 'center',
  },
  evaluationGridRowScaleLabel: {
    flexBasis: '4mm',
    flexGrow: 0,
    flexShrink: 0,
  },
}

export default styles;
