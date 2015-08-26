# Run by typing : R --vanilla --args expressionfile=~/location/ formula=FORMULA-SCALED-PREDICTOR-ILMN_ID.txt outputtxt=OUTCOME-SCALED-PREDICTOR-ILMN_ID.txt < SCALED-PREDICTOR-ILMN.R

# print command line args
commandArgs(trailingOnly=TRUE)
options(expressions=20000)

# process command line arguments
for (e in commandArgs(trailingOnly=TRUE)) {
  ta = strsplit(e,"=",fixed=TRUE)
  if(!is.null(ta[[1]][2])) {
    assign(ta[[1]][1],ta[[1]][2])
  } else {
    assign(ta[[1]][1],TRUE)
  }
}

library(preprocessCore)
options(expression=30000)

expressionfile		# input residuals file
formula			# prediction formula
outputtxt			# results file: association of delta age with phenotypes of interest


expression <- read.table(expressionfile, header = TRUE, row.names = 1, stringsAsFactors=FALSE)
dim(expression)

nPrbs <- dim(expression)[1]
nSamples <- dim(expression)[2]

oexpression <- expression[order(rownames(expression)),]

genes_needed <- read.table("ILMNID.txt", header = FALSE, stringsAsFactors=FALSE)

k <- rownames(oexpression)
l <- genes_needed[,1]

genes.present <- genes_needed[l %in% k,]
genes.notpresent <- genes_needed[!l %in% k,]

ngenes.notpresent <- length(genes.notpresent)
missing.genes=matrix(0,ngenes.notpresent,nSamples)
rownames(missing.genes) = genes.notpresent
colnames(missing.genes) = colnames(oexpression)
all.genes <- rbind(oexpression,missing.genes)

#myGENES <- cbind(ILMNID = rownames(all.genes), all.genes)
#rownames(myGENES) <- NULL
#write.table(myGENES, "FULL-ILMN-RES-MATRIX-SCALED.txt", , quote=FALSE, row.names=F, sep="\t")



# Transpose Gene Expression Data
t.all.genes <- t(all.genes)
dim(t.all.genes)


selection = as.data.frame(t.all.genes)


# Load the formula - calculated using the prediction algorithm
age.predicted <- matrix(NA,nSamples, 1)

results=matrix(NA,nSamples,1)
colnames(results) = c("TA")
rownames(results) = rownames(t.all.genes)

myModel = readLines(formula)
myModel = eval(parse(text=paste(myModel)))
age.predicted = myModel

rownames(results) = rownames(t.all.genes)

predictor <- age.predicted
length(predictor)

mean(predictor)
sd(predictor)

results[,1] = predictor

myDF <- cbind(SAMPLEID = rownames(results), results)
rownames(myDF) <- NULL
write.table(myDF, outputtxt, quote=FALSE, row.names=F, sep="\t")

